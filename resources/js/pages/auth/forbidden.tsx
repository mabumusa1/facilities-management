// Components
import { Head, Link } from "@inertiajs/react";
import { Button } from "@/components/ui/button";

export default function Forbidden() {
    return (
        <>
            <Head title="Access Denied" />

            <div className="space-y-6 text-center">
                <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                    <svg
                        className="h-8 w-8 text-red-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        strokeWidth="1.5"
                        stroke="currentColor"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
                        />
                    </svg>
                </div>

                <p className="text-sm text-gray-600">
                    You do not have permission to access this resource.
                </p>

                <div className="flex justify-center gap-4">
                    <Link href="/">
                        <Button variant="secondary">Go Home</Button>
                    </Link>
                    <Button
                        variant="outline"
                        onClick={() => window.history.back()}
                    >
                        Go Back
                    </Button>
                </div>
            </div>
        </>
    );
}

Forbidden.layout = {
    title: "403 - Forbidden",
    description: "You do not have permission to access this resource.",
};
