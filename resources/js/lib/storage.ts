import { currentUser } from "./auth";

export interface Attempt {
    id: number;
    user_id: number | null;
    question_id: number;
    answer_text: string;
    estimated_band: number | null;
    strengths: string[];
    areas_to_improve: string[];
    created_at: string;
    updated_at: string;
    question?: {
        id: number;
        part: number;
        topic: string;
        prompt: string;
    } | null;
}

const STORAGE_KEY = "speaking_attempts";

export function loadAttempts(userId?: number | null): Attempt[] {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return [];
        const attempts = JSON.parse(raw) as Attempt[];
        const targetId = userId !== undefined ? userId : currentUser.value?.id;
        if (targetId) {
            return attempts.filter((a) => a.user_id === targetId);
        }
        return attempts;
    } catch {
        return [];
    }
}

export function saveAttempt(attempt: Attempt): void {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        const attempts: Attempt[] = raw ? JSON.parse(raw) : [];
        const index = attempts.findIndex((a) => a.id === attempt.id);
        if (index >= 0) {
            attempts[index] = attempt;
        } else {
            attempts.unshift(attempt);
        }
        localStorage.setItem(STORAGE_KEY, JSON.stringify(attempts));
    } catch {
        // ignore storage errors
    }
}

export function findAttempt(id: number | string, userId?: number | null): Attempt | undefined {
    return loadAttempts(userId).find((a) => a.id === Number(id));
}
