<script setup lang="ts">
import { RouterLink, RouterView, useRouter } from "vue-router";
import { clearAuth, currentUser, getToken } from "./lib/auth";

const router = useRouter();

async function logout() {
    try {
        const token = getToken();
        if (token) {
            await fetch("/api/logout", {
                method: "POST",
                headers: { Authorization: `Bearer ${token}` },
            });
        }
    } catch {
        // ignore network errors on logout
    } finally {
        clearAuth();
        router.push({ name: "login" });
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-3xl items-center justify-between px-4 py-4">
                <h1 class="text-lg font-semibold text-gray-800">
                    IELTS Speaking Dashboard
                </h1>
                <div class="flex items-center gap-4 text-sm">
                    <RouterLink to="/speaking" class="text-gray-600 hover:text-gray-900">
                        Attempts
                    </RouterLink>
                    <template v-if="currentUser">
                        <span class="text-gray-500">{{ currentUser.name }}</span>
                        <button
                            @click="logout"
                            class="text-gray-600 hover:text-gray-900"
                        >
                            Logout
                        </button>
                    </template>
                    <RouterLink v-else :to="{ name: 'login' }" class="text-gray-600 hover:text-gray-900">
                        Login
                    </RouterLink>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-3xl px-4 py-6">
            <RouterView />
        </main>
    </div>
</template>
