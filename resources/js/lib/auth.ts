import { ref } from "vue";

export interface AuthUser {
    id: number;
    name: string;
    email: string;
}

const TOKEN_KEY = "speaking_token";
const USER_KEY = "speaking_user";

export function getToken(): string | null {
    return localStorage.getItem(TOKEN_KEY);
}

export function getUser(): AuthUser | null {
    try {
        const raw = localStorage.getItem(USER_KEY);
        return raw ? (JSON.parse(raw) as AuthUser) : null;
    } catch {
        return null;
    }
}

export const currentUser = ref<AuthUser | null>(getUser());

export function setAuth(token: string, user: AuthUser): void {
    localStorage.setItem(TOKEN_KEY, token);
    localStorage.setItem(USER_KEY, JSON.stringify(user));
    currentUser.value = user;
}

export function clearAuth(): void {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
    currentUser.value = null;
}

export function isAuthenticated(): boolean {
    return Boolean(getToken());
}
