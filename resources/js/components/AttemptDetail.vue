<script setup lang="ts">
import { useRouter } from "vue-router";
import { findAttempt } from "../lib/storage";

const props = defineProps<{ id: string | number }>();
const router = useRouter();

const attempt = findAttempt(props.id);
</script>

<template>
    <div>
        <button
            @click="router.push({ name: 'attempts' })"
            class="mb-4 text-sm font-medium text-blue-600 hover:underline"
        >
            &larr; Back to attempts
        </button>

        <p v-if="!attempt" class="rounded-md bg-gray-50 p-6 text-center text-gray-500">
            Attempt not found in this browser.
        </p>

        <div v-else class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">
                    {{ attempt.question?.topic ?? "Speaking Attempt" }}
                </h2>
                <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800">
                    Band {{ attempt.estimated_band ?? "N/A" }}
                </span>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-gray-500">
                    Question
                </h3>
                <p class="text-gray-800">{{ attempt.question?.prompt ?? "—" }}</p>
                <p v-if="attempt.question" class="mt-1 text-sm text-gray-500">
                    Part {{ attempt.question.part }} &middot; {{ attempt.question.topic }}
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-gray-500">
                    Your Answer
                </h3>
                <p class="whitespace-pre-wrap text-gray-800">{{ attempt.answer_text }}</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                    <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-green-700">
                        Strengths
                    </h3>
                    <ul v-if="attempt.strengths?.length" class="list-disc space-y-1 pl-5 text-gray-800">
                        <li v-for="(item, i) in attempt.strengths" :key="i">{{ item }}</li>
                    </ul>
                    <p v-else class="text-gray-500">—</p>
                </div>

                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-amber-700">
                        Areas to Improve
                    </h3>
                    <ul v-if="attempt.areas_to_improve?.length" class="list-disc space-y-1 pl-5 text-gray-800">
                        <li v-for="(item, i) in attempt.areas_to_improve" :key="i">{{ item }}</li>
                    </ul>
                    <p v-else class="text-gray-500">—</p>
                </div>
            </div>
        </div>
    </div>
</template>
