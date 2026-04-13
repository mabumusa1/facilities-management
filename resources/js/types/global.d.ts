import type { Auth } from "@/types/auth";

declare module "@inertiajs/core" {
    export interface InertiaConfig {
        sharedPageProps: {
            auth: Auth;
            name: string;
            sidebarOpen: boolean;
            [key: string]: unknown;
        };
    }
}
