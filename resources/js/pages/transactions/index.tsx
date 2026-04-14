import { Head, router } from "@inertiajs/react";
import { ArrowDownLeft, ArrowUpRight, Search } from "lucide-react";
import { useMemo, useState } from "react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { index as transactionsIndex } from "@/routes/transactions";

type FilterType = "all" | "money_in" | "money_out";

interface Transaction {
    id: number;
    amount: number;
    paid: number;
    left: number;
    direction: "money_in" | "money_out";
    details: string | null;
    lease_number: string | null;
    is_paid: boolean;
    due_on: string | null;
    created_at: string | null;
    type: string | null;
    category: string | null;
    status: string | null;
    assignee: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedTransactions {
    data: Transaction[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
}

interface Props {
    transactions: PaginatedTransactions;
    filters: {
        filter_type: FilterType;
        from: string;
        to: string;
        search: string;
    };
    tabs: Array<{
        name: string;
        filter_type: FilterType;
    }>;
    currency: string;
}

function formatAmount(value: number, currency: string): string {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency,
        minimumFractionDigits: 2,
    }).format(value);
}

export default function TransactionsIndex({
    transactions,
    filters,
    tabs,
    currency,
}: Props) {
    const [search, setSearch] = useState(filters.search ?? "");
    const [fromDate, setFromDate] = useState(filters.from ?? "");
    const [toDate, setToDate] = useState(filters.to ?? "");
    const [activeFilter, setActiveFilter] = useState<FilterType>(
        filters.filter_type ?? "all",
    );

    const summary = useMemo(() => {
        return transactions.data.reduce(
            (accumulator, transaction) => {
                if (transaction.direction === "money_in") {
                    accumulator.moneyIn += Math.abs(transaction.amount);
                } else {
                    accumulator.moneyOut += Math.abs(transaction.amount);
                }

                return accumulator;
            },
            { moneyIn: 0, moneyOut: 0 },
        );
    }, [transactions.data]);

    const applyFilters = (
        overrides: Partial<{
            filter_type: FilterType;
            from: string;
            to: string;
            search: string;
        }> = {},
    ) => {
        const nextFilter = overrides.filter_type ?? activeFilter;
        const nextFrom = overrides.from ?? fromDate;
        const nextTo = overrides.to ?? toDate;
        const nextSearch = (overrides.search ?? search).trim();

        setActiveFilter(nextFilter);

        router.get(
            transactionsIndex(),
            {
                filter_type: nextFilter,
                from: nextFrom || undefined,
                to: nextTo || undefined,
                search: nextSearch || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const clearFilters = () => {
        setSearch("");
        setFromDate("");
        setToDate("");
        setActiveFilter("all");
        router.get(transactionsIndex(), {}, { preserveState: true, replace: true });
    };

    return (
        <>
            <Head title="Transactions" />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-start justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Transactions</h1>
                        <p className="text-muted-foreground">
                            Track incoming and outgoing payments.
                        </p>
                    </div>
                    <div className="text-right">
                        <p className="text-muted-foreground text-xs uppercase tracking-wide">
                            Net Position
                        </p>
                        <p className="text-lg font-semibold">
                            {formatAmount(summary.moneyIn - summary.moneyOut, currency)}
                        </p>
                    </div>
                </div>

                <div className="grid gap-3 md:grid-cols-3">
                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Money In</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-xl font-semibold text-emerald-700">
                                {formatAmount(summary.moneyIn, currency)}
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Money Out</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-xl font-semibold text-red-700">
                                {formatAmount(summary.moneyOut, currency)}
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Total Records</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-xl font-semibold">{transactions.total}</p>
                        </CardContent>
                    </Card>
                </div>

                <div className="flex flex-wrap gap-2">
                    {tabs.map((tab) => (
                        <Button
                            key={tab.filter_type}
                            variant={activeFilter === tab.filter_type ? "default" : "outline"}
                            onClick={() => applyFilters({ filter_type: tab.filter_type })}
                        >
                            {tab.name}
                        </Button>
                    ))}
                </div>

                <Card>
                    <CardContent className="pt-6">
                        <div className="grid gap-3 md:grid-cols-[1fr_180px_180px_auto_auto]">
                            <div className="relative">
                                <Search className="text-muted-foreground absolute top-2.5 left-2.5 h-4 w-4" />
                                <Input
                                    value={search}
                                    onChange={(event) => setSearch(event.target.value)}
                                    onKeyDown={(event) => {
                                        if (event.key === "Enter") {
                                            applyFilters({ search });
                                        }
                                    }}
                                    placeholder="Search by lease number or details"
                                    className="pl-8"
                                />
                            </div>

                            <Input
                                type="date"
                                value={fromDate}
                                onChange={(event) => setFromDate(event.target.value)}
                            />

                            <Input
                                type="date"
                                value={toDate}
                                onChange={(event) => setToDate(event.target.value)}
                            />

                            <Button onClick={() => applyFilters({ search })}>Search</Button>
                            <Button variant="outline" onClick={clearFilters}>
                                Clear
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>
                            {transactions.total} {transactions.total === 1 ? "Transaction" : "Transactions"}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {transactions.data.length === 0 ? (
                            <p className="text-muted-foreground py-8 text-center">
                                No transactions found for the selected filters.
                            </p>
                        ) : (
                            <div className="space-y-2">
                                {transactions.data.map((transaction) => (
                                    <div
                                        key={transaction.id}
                                        className="hover:bg-muted/40 flex items-center justify-between rounded-lg border p-4 transition-colors"
                                    >
                                        <div className="space-y-1">
                                            <div className="flex items-center gap-2">
                                                <Badge
                                                    variant={
                                                        transaction.direction === "money_in"
                                                            ? "default"
                                                            : "destructive"
                                                    }
                                                >
                                                    {transaction.direction === "money_in" ? (
                                                        <ArrowDownLeft className="mr-1 h-3 w-3" />
                                                    ) : (
                                                        <ArrowUpRight className="mr-1 h-3 w-3" />
                                                    )}
                                                    {transaction.direction === "money_in"
                                                        ? "Money In"
                                                        : "Money Out"}
                                                </Badge>
                                                <Badge variant={transaction.is_paid ? "default" : "secondary"}>
                                                    {transaction.is_paid ? "Paid" : "Unpaid"}
                                                </Badge>
                                                {transaction.status && (
                                                    <Badge variant="outline">{transaction.status}</Badge>
                                                )}
                                            </div>
                                            <p className="font-medium">
                                                {transaction.lease_number
                                                    ? `Lease ${transaction.lease_number}`
                                                    : `Transaction #${transaction.id}`}
                                            </p>
                                            <p className="text-muted-foreground text-sm">
                                                {transaction.details ?? "No description provided"}
                                            </p>
                                            <p className="text-muted-foreground text-xs">
                                                {transaction.assignee
                                                    ? `Assignee: ${transaction.assignee}`
                                                    : "Unassigned"}
                                                {transaction.due_on ? ` • Due ${transaction.due_on}` : ""}
                                            </p>
                                        </div>

                                        <div className="text-right">
                                            <p
                                                className={`text-lg font-semibold ${
                                                    transaction.direction === "money_in"
                                                        ? "text-emerald-700"
                                                        : "text-red-700"
                                                }`}
                                            >
                                                {formatAmount(Math.abs(transaction.amount), currency)}
                                            </p>
                                            <p className="text-muted-foreground text-xs">
                                                Paid {formatAmount(Math.abs(transaction.paid), currency)}
                                            </p>
                                            <p className="text-muted-foreground text-xs">
                                                Left {formatAmount(Math.abs(transaction.left), currency)}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}

                        {transactions.last_page > 1 && (
                            <div className="mt-4 flex items-center justify-center gap-2">
                                {transactions.links.map((link, index) => (
                                    <Button
                                        key={`${link.label}-${index}`}
                                        variant={link.active ? "default" : "outline"}
                                        size="sm"
                                        disabled={!link.url}
                                        onClick={() => {
                                            if (link.url) {
                                                router.get(link.url, {}, { preserveState: true });
                                            }
                                        }}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

TransactionsIndex.layout = {
    breadcrumbs: [{ title: "Transactions", href: transactionsIndex() }],
};
