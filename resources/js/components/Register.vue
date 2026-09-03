<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { setAuth } from "../lib/auth";

const router = useRouter();

const name = ref("");
const email = ref("");
const password = ref("");
const loading = ref(false);
const error = ref<string | null>(null);

async function register() {
    if (!name.value.trim() || !email.value || !password.value) {
        error.value = "Please fill in all fields.";
        return;
    }
    if (password.value.length < 8) {
        error.value = "Password must be at least 8 characters.";
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        const response = await fetch("/api/register", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                name: name.value,
                email: email.value,
                password: password.value,
            }),
        });

        const data = await response.json().catch(() => null);

        if (!response.ok) {
            const message =
                data?.message ??
                (data?.errors ? Object.values(data.errors).flat().join(" ") : "Registration failed.");
            throw new Error(message);
        }

        setAuth(data.token, data.user);
        router.push({ name: "submit" });
    } catch (err) {
        error.value = err instanceof Error ? err.message : "Registration failed.";
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="mx-auto max-w-md space-y-6">
        <button
            @click="router.push({ name: 'login' })"
            class="text-sm font-medium text-blue-600 hover:underline"
        >
            &larr; Back to login
        </button>

        <h2 class="text-xl font-semibold text-gray-800">Register</h2>

        <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                <input
                    v-model="name"
                    type="text"
                    placeholder="Your name"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-800"
                />
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <input
                    v-model="email"
                    type="email"
                    placeholder="you@example.com"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-800"
                />
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                <input
                    v-model="password"
                    type="password"
                    placeholder="At least 8 characters"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-800"
                />
            </div>

            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>

            <button
                @click="register"
                :disabled="loading"
                class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
            >
                {{ loading ? "Registering..." : "Register" }}
            </button>
        </div>
    </div>
</template>
