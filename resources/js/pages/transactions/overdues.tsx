import { Head } from "@inertiajs/react";

export default function TransactionOverdues() {
    return (
        <>
            <Head title="Overdue Transactions" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <h1 className="text-2xl font-bold">Overdue Transactions</h1>
            </div>
        </>
    );
}
