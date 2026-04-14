import { Head } from "@inertiajs/react";

export default function TransactionJournalEntries() {
    return (
        <>
            <Head title="Journal Entries" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <h1 className="text-2xl font-bold">Journal Entries</h1>
            </div>
        </>
    );
}
