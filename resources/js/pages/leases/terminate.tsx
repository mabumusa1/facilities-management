import { Head, Link, useForm } from "@inertiajs/react";
import {
    AlertTriangle,
    ArrowLeft,
    XCircle,
    Calendar,
    DollarSign,
    User,
    Building2,
    FileText,
} from "lucide-react";
import type { FormEvent } from "react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";
import { Textarea } from "@/components/ui/textarea";
import AppLayout from "@/layouts/app-layout";
import type { BreadcrumbItem } from "@/types";

interface TerminationSummary {
    lease_id: number;
    contract_number: string;
    tenant_name: string | null;
    start_date: string;
    end_date: string;
    days_remaining: number;
    is_early_termination: boolean;
    security_deposit: number;
    rental_total_amount: number;
}

interface Unit {
    id: number;
    name: string;
}

interface Lease {
    id: number;
    contract_number: string;
    tenant?: { id: number; name: string; email: string };
    units: Unit[];
    community?: { id: number; name: string };
    building?: { id: number; name: string };
    start_date: string;
    end_date: string;
    rental_total_amount: number;
    security_deposit_amount: number | null;
    status?: { id: number; name: string };
}

interface Props {
    lease: Lease;
    terminationSummary: TerminationSummary;
}

export default function LeaseTerminate({ lease, terminationSummary }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: "Leases", href: "/leases" },
        { title: lease.contract_number, href: `/leases/${lease.id}` },
        { title: "Terminate", href: "#" },
    ];

    const { data, setData, post, processing, errors } = useForm({
        termination_date: new Date().toISOString().split("T")[0],
        termination_reason: "",
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post(`/leases/${lease.id}/terminate`);
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    };

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: "SAR",
            minimumFractionDigits: 0,
        }).format(amount);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Terminate Lease - ${lease.contract_number}`} />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-2xl font-bold tracking-tight text-destructive">
                            Terminate Lease
                        </h2>
                        <p className="text-muted-foreground">
                            Cancel lease {lease.contract_number} before its end
                            date
                        </p>
                    </div>
                    <Link href={`/leases/${lease.id}`}>
                        <Button variant="outline">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Lease
                        </Button>
                    </Link>
                </div>

                {/* Warning Banner */}
                <div className="rounded-lg border border-destructive/50 bg-destructive/10 p-4">
                    <div className="flex items-start gap-3">
                        <AlertTriangle className="h-5 w-5 text-destructive mt-0.5" />
                        <div>
                            <p className="font-medium text-destructive">
                                This action cannot be undone
                            </p>
                            <p className="text-sm text-muted-foreground">
                                Terminating a lease will cancel the agreement
                                and release all associated units.
                                {terminationSummary.is_early_termination && (
                                    <span className="block mt-1 text-destructive">
                                        This is an early termination with{" "}
                                        {terminationSummary.days_remaining} days
                                        remaining.
                                    </span>
                                )}
                            </p>
                        </div>
                    </div>
                </div>

                {/* Lease Summary */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-2">
                            <FileText className="h-5 w-5" />
                            <CardTitle>Lease Summary</CardTitle>
                        </div>
                        <CardDescription>
                            Review the lease details before termination
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid gap-4 md:grid-cols-4">
                            <div className="flex items-center gap-3">
                                <div className="rounded-full bg-primary/10 p-2">
                                    <FileText className="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">
                                        Contract
                                    </p>
                                    <p className="font-medium">
                                        {lease.contract_number}
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="rounded-full bg-primary/10 p-2">
                                    <User className="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">
                                        Tenant
                                    </p>
                                    <p className="font-medium">
                                        {lease.tenant?.name || "N/A"}
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="rounded-full bg-primary/10 p-2">
                                    <Calendar className="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">
                                        Period
                                    </p>
                                    <p className="font-medium">
                                        {formatDate(lease.start_date)} -{" "}
                                        {formatDate(lease.end_date)}
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="rounded-full bg-primary/10 p-2">
                                    <DollarSign className="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">
                                        Rental Amount
                                    </p>
                                    <p className="font-medium">
                                        {formatCurrency(
                                            terminationSummary.rental_total_amount,
                                        )}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <Separator className="my-4" />

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <p className="text-sm font-medium mb-2">
                                    Units ({lease.units.length})
                                </p>
                                <div className="flex flex-wrap gap-2">
                                    {lease.units.map((unit) => (
                                        <Badge
                                            key={unit.id}
                                            variant="secondary"
                                        >
                                            <Building2 className="mr-1 h-3 w-3" />
                                            {unit.name}
                                        </Badge>
                                    ))}
                                </div>
                            </div>
                            <div>
                                <p className="text-sm font-medium mb-2">
                                    Security Deposit
                                </p>
                                <p className="text-lg font-semibold">
                                    {terminationSummary.security_deposit > 0
                                        ? formatCurrency(
                                              terminationSummary.security_deposit,
                                          )
                                        : "No deposit"}
                                </p>
                            </div>
                        </div>

                        {terminationSummary.is_early_termination && (
                            <>
                                <Separator className="my-4" />
                                <div className="rounded-lg bg-amber-50 border border-amber-200 p-3">
                                    <p className="text-sm text-amber-800">
                                        <strong>Early Termination:</strong> This
                                        lease has{" "}
                                        {terminationSummary.days_remaining} days
                                        remaining until the scheduled end date (
                                        {formatDate(
                                            terminationSummary.end_date,
                                        )}
                                        ). Early termination may be subject to
                                        fees or penalties as per the lease
                                        agreement.
                                    </p>
                                </div>
                            </>
                        )}
                    </CardContent>
                </Card>

                {/* Termination Form */}
                <form onSubmit={handleSubmit}>
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <XCircle className="h-5 w-5 text-destructive" />
                                <CardTitle>Termination Details</CardTitle>
                            </div>
                            <CardDescription>
                                Provide the termination information
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="termination_date">
                                        Termination Date *
                                    </Label>
                                    <Input
                                        id="termination_date"
                                        type="date"
                                        value={data.termination_date}
                                        onChange={(e) =>
                                            setData(
                                                "termination_date",
                                                e.target.value,
                                            )
                                        }
                                    />
                                    {errors.termination_date && (
                                        <p className="text-sm text-destructive">
                                            {errors.termination_date}
                                        </p>
                                    )}
                                    <p className="text-xs text-muted-foreground">
                                        The effective date when the lease will
                                        be terminated
                                    </p>
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="termination_reason">
                                    Termination Reason
                                </Label>
                                <Textarea
                                    id="termination_reason"
                                    placeholder="Enter the reason for termination..."
                                    rows={4}
                                    value={data.termination_reason}
                                    onChange={(e) =>
                                        setData(
                                            "termination_reason",
                                            e.target.value,
                                        )
                                    }
                                />
                                {errors.termination_reason && (
                                    <p className="text-sm text-destructive">
                                        {errors.termination_reason}
                                    </p>
                                )}
                                <p className="text-xs text-muted-foreground">
                                    Optional. Document the reason for
                                    termination for record-keeping.
                                </p>
                            </div>

                            <Separator />

                            <div className="flex justify-between">
                                <Link href={`/leases/${lease.id}`}>
                                    <Button type="button" variant="outline">
                                        Cancel
                                    </Button>
                                </Link>
                                <Button
                                    type="submit"
                                    variant="destructive"
                                    disabled={processing}
                                >
                                    {processing ? (
                                        <>Processing...</>
                                    ) : (
                                        <>
                                            <XCircle className="mr-2 h-4 w-4" />
                                            Terminate Lease
                                        </>
                                    )}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </form>
            </div>
        </AppLayout>
    );
}
