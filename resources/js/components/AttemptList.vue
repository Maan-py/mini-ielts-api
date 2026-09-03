<script setup lang="ts">
import { useRouter } from "vue-router";
import { loadAttempts } from "../lib/storage";

const router = useRouter();
const attempts = loadAttempts();

function goToDetail(id: number) {
    router.push({ name: "attempt-detail", params: { id } });
}

function formatDate(dateStr: string): string {
    const date = new Date(dateStr);
    return date.toLocaleString();
}
</script>

<template>
    <div>
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                Speaking Attempts
            </h2>
            <button
                @click="router.push({ name: 'submit' })"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
                + Submit Answer
            </button>
        </div>

        <p v-if="attempts.length === 0" class="rounded-md bg-gray-50 p-6 text-center text-gray-500">
            No attempts yet. Submit your first speaking answer.
        </p>

        <ul v-else class="divide-y divide-gray-200 overflow-hidden rounded-lg border border-gray-200 bg-white">
            <li
                v-for="attempt in attempts"
                :key="attempt.id"
                class="flex cursor-pointer items-center justify-between px-4 py-3 hover:bg-gray-50"
                @click="goToDetail(attempt.id)"
            >
                <div class="min-w-0">
                    <p class="truncate font-medium text-gray-800">
                        {{ attempt.question?.topic ?? "Speaking Attempt" }}
                    </p>
                    <p class="text-sm text-gray-500">{{ formatDate(attempt.created_at) }}</p>
                </div>
                <span
                    class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800"
                >
                    Band {{ attempt.estimated_band ?? "N/A" }}
                </span>
            </li>
        </ul>
    </div>
</template>
