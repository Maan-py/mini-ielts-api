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

export function loadAttempts(): Attempt[] {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        return raw ? (JSON.parse(raw) as Attempt[]) : [];
    } catch {
        return [];
    }
}

export function saveAttempt(attempt: Attempt): void {
    const attempts = loadAttempts();
    const index = attempts.findIndex((a) => a.id === attempt.id);
    if (index >= 0) {
        attempts[index] = attempt;
    } else {
        attempts.unshift(attempt);
    }
    localStorage.setItem(STORAGE_KEY, JSON.stringify(attempts));
}

export function findAttempt(id: number | string): Attempt | undefined {
    return loadAttempts().find((a) => a.id === Number(id));
}
