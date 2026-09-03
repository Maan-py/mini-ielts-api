<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { saveAttempt } from "../lib/storage";
import { getToken, isAuthenticated } from "../lib/auth";

interface Question {
    id: number;
    part: number;
    topic: string;
    prompt: string;
}

const router = useRouter();

const authed = ref(isAuthenticated());
const questions = ref<Question[]>([]);
const loadingQuestions = ref(true);
const loadError = ref<string | null>(null);

const selectedQuestionId = ref<string>("");
const answer = ref("");
const submitting = ref(false);
const submitError = ref<string | null>(null);

if (!authed.value) {
    router.replace({ name: "login" });
}

onMounted(async () => {
    try {
        const response = await fetch("/api/speaking/questions");
        if (!response.ok) {
            throw new Error("Failed to fetch questions");
        }
        questions.value = await response.json();
    } catch {
        loadError.value = "Failed to load speaking questions.";
    } finally {
        loadingQuestions.value = false;
    }
});

async function submit() {
    if (!selectedQuestionId.value || !answer.value.trim()) {
        submitError.value = "Please select a question and write an answer.";
        return;
    }

    submitting.value = true;
    submitError.value = null;

    try {
        const response = await fetch("/api/speaking/submit", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${getToken()}`,
            },
            body: JSON.stringify({
                question_id: Number(selectedQuestionId.value),
                answer: answer.value,
            }),
        });

        if (response.status === 401) {
            router.replace({ name: "login" });
            return;
        }

        if (!response.ok) {
            const err = await response.json().catch(() => null);
            throw new Error(err?.message ?? "Submission failed");
        }

        const data = await response.json();
        if (data?.attempt) {
            const question = questions.value.find((q) => q.id === data.attempt.question_id);
            saveAttempt({ ...data.attempt, question: question ?? null });
            router.push({ name: "attempt-detail", params: { id: data.attempt.id } });
        }
    } catch (err) {
        submitError.value =
            err instanceof Error ? err.message : "Failed to submit answer.";
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <div class="mx-auto max-w-2xl space-y-6">
        <button
            @click="router.push({ name: 'attempts' })"
            class="text-sm font-medium text-blue-600 hover:underline"
        >
            &larr; Back to attempts
        </button>

        <h2 class="text-xl font-semibold text-gray-800">Submit Speaking Answer</h2>

        <p v-if="loadingQuestions" class="text-gray-500">Loading questions...</p>
        <p v-else-if="loadError" class="text-red-600">{{ loadError }}</p>

        <div v-else class="space-y-4 rounded-lg border border-gray-200 bg-white p-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Question</label>
                <select
                    v-model="selectedQuestionId"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-800"
                >
                    <option value="" disabled>Select a question</option>
                    <option v-for="q in questions" :key="q.id" :value="q.id">
                        Part {{ q.part }} &middot; {{ q.topic }}
                    </option>
                </select>
            </div>

            <div v-if="selectedQuestionId">
                <p class="mb-1 text-sm text-gray-600">
                    {{ questions.find((q) => q.id === Number(selectedQuestionId))?.prompt }}
                </p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Your Answer</label>
                <textarea
                    v-model="answer"
                    rows="6"
                    placeholder="Type your answer..."
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-800"
                ></textarea>
            </div>

            <p v-if="submitError" class="text-sm text-red-600">{{ submitError }}</p>

            <button
                @click="submit"
                :disabled="submitting"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
            >
                {{ submitting ? "Submitting..." : "Submit" }}
            </button>
        </div>
    </div>
</template>
