import type { Auth } from "@/types/auth";

declare module "@inertiajs/core" {
    interface PageProps {
        auth: Auth;
        name: string;
        sidebarOpen: boolean;
    }
}
