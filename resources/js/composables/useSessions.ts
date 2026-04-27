import { useHttp } from '@inertiajs/vue3';
import type { Ref } from 'vue';
import { ref } from 'vue';
import { index, destroy, revokeAll } from '@/routes/sessions';
import type { ActiveSession } from '@/types';

export type UseSessionsReturn = {
    sessions: Ref<ActiveSession[]>;
    loading: Ref<boolean>;
    revoking: Ref<string | null>;
    revokingAll: Ref<boolean>;
    error: Ref<string | null>;
    fetchSessions: () => Promise<void>;
    revokeSession: (id: string) => Promise<boolean>;
    revokeAllSessions: () => Promise<boolean>;
};

const sessions = ref<ActiveSession[]>([]);
const loading = ref<boolean>(false);
const revoking = ref<string | null>(null);
const revokingAll = ref<boolean>(false);
const error = ref<string | null>(null);

export const useSessions = (): UseSessionsReturn => {
    const http = useHttp();

    const fetchSessions = async (): Promise<void> => {
        loading.value = true;
        error.value = null;

        try {
            sessions.value = (await http.submit(index())) as ActiveSession[];
        } catch {
            error.value = 'Failed to load sessions.';
            sessions.value = [];
        } finally {
            loading.value = false;
        }
    };

    const revokeSession = async (id: string): Promise<boolean> => {
        revoking.value = id;
        error.value = null;

        try {
            await http.submit(destroy(id));
            sessions.value = sessions.value.filter((s) => s.id !== id);

            return true;
        } catch {
            error.value = 'Failed to revoke session.';

            return false;
        } finally {
            revoking.value = null;
        }
    };

    const revokeAllSessions = async (): Promise<boolean> => {
        revokingAll.value = true;
        error.value = null;

        try {
            await http.submit(revokeAll());
            sessions.value = sessions.value.filter((s) => s.is_current);

            return true;
        } catch {
            error.value = 'Failed to revoke all sessions.';

            return false;
        } finally {
            revokingAll.value = false;
        }
    };

    return {
        sessions,
        loading,
        revoking,
        revokingAll,
        error,
        fetchSessions,
        revokeSession,
        revokeAllSessions,
    };
};
