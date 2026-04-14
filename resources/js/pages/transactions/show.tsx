import { Head } from "@inertiajs/react";

export default function TransactionShow() {
    return (
        <>
            <Head title="Transaction Details" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <h1 className="text-2xl font-bold">Transaction Details</h1>
            </div>
        </>
    );
}
