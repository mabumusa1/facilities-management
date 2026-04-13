// Components
import { Head, Link } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { logout } from "@/routes";

export default function NoAccess() {
    return (
        <>
            <Head title="No Access" />

            <div className="space-y-6 text-center">
                <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-yellow-100">
                    <svg
                        className="h-8 w-8 text-yellow-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        strokeWidth="1.5"
                        stroke="currentColor"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                        />
                    </svg>
                </div>

                <p className="text-sm text-gray-600">
                    Your account is pending verification. Please contact your
                    administrator for access.
                </p>

                <Link href={logout()} method="post" as="button">
                    <Button variant="secondary">Log out</Button>
                </Link>
            </div>
        </>
    );
}

NoAccess.layout = {
    title: "Access Pending",
    description:
        "Your account requires verification before you can access the system.",
};
