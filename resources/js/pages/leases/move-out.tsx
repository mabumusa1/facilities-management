import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, LogOut, Calendar, DollarSign, User, Building2, FileText, ClipboardCheck, AlertCircle } from 'lucide-react';
import type { FormEvent } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import type {BreadcrumbItem} from '@/types';

interface MoveOutSummary {
    lease_id: number;
    contract_number: string;
    tenant_name: string | null;
    start_date: string;
    end_date: string;
    is_after_end_date: boolean;
    security_deposit: number;
    rental_total_amount: number;
    unpaid_amount: number;
    units: Array<{ id: number; name: string }>;
}

interface Unit {
    id: number;
    name: string;
}

interface Transaction {
    id: number;
    amount: string;
    due_date: string;
    is_paid: boolean;
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
    transactions?: Transaction[];
}

interface Props {
    lease: Lease;
    moveOutSummary: MoveOutSummary;
}

export default function LeaseMoveOut({ lease, moveOutSummary }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Leases', href: '/leases' },
        { title: lease.contract_number, href: `/leases/${lease.id}` },
        { title: 'Move Out', href: '#' },
    ];

    const { data, setData, post, processing, errors } = useForm({
        move_out_date: new Date().toISOString().split('T')[0],
        inspection_notes: '',
        deposit_deductions: '',
        deposit_refund_amount: moveOutSummary.security_deposit,
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post(`/leases/${lease.id}/move-out`);
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'SAR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Move Out - ${lease.contract_number}`} />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-2xl font-bold tracking-tight">Move Out</h2>
                        <p className="text-muted-foreground">
                            Complete move-out process for lease {lease.contract_number}
                        </p>
                    </div>
                    <Link href={`/leases/${lease.id}`}>
                        <Button variant="outline">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Lease
                        </Button>
                    </Link>
                </div>

                {/* Outstanding Balance Warning */}
                {moveOutSummary.unpaid_amount > 0 && (
                    <div className="rounded-lg border border-amber-200 bg-amber-50 p-4">
                        <div className="flex items-start gap-3">
                            <AlertCircle className="h-5 w-5 text-amber-600 mt-0.5" />
                            <div>
                                <p className="font-medium text-amber-800">Outstanding Balance</p>
                                <p className="text-sm text-amber-700">
                                    This lease has an unpaid balance of {formatCurrency(moveOutSummary.unpaid_amount)}.
                                    Please ensure all payments are settled before completing the move-out.
                                </p>
                            </div>
                        </div>
                    </div>
                )}

                {/* Lease Summary */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-2">
                            <FileText className="h-5 w-5" />
                            <CardTitle>Lease Summary</CardTitle>
                        </div>
                        <CardDescription>Review the lease details before move-out</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid gap-4 md:grid-cols-4">
                            <div className="flex items-center gap-3">
                                <div className="rounded-full bg-primary/10 p-2">
                                    <FileText className="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Contract</p>
                                    <p className="font-medium">{lease.contract_number}</p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="rounded-full bg-primary/10 p-2">
                                    <User className="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Tenant</p>
                                    <p className="font-medium">{lease.tenant?.name || 'N/A'}</p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="rounded-full bg-primary/10 p-2">
                                    <Calendar className="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Lease Period</p>
                                    <p className="font-medium">
                                        {formatDate(lease.start_date)} - {formatDate(lease.end_date)}
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="rounded-full bg-primary/10 p-2">
                                    <DollarSign className="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Rental Amount</p>
                                    <p className="font-medium">{formatCurrency(moveOutSummary.rental_total_amount)}</p>
                                </div>
                            </div>
                        </div>

                        <Separator className="my-4" />

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <p className="text-sm font-medium mb-2">Units to be Released ({lease.units.length})</p>
                                <div className="flex flex-wrap gap-2">
                                    {lease.units.map((unit) => (
                                        <Badge key={unit.id} variant="secondary">
                                            <Building2 className="mr-1 h-3 w-3" />
                                            {unit.name}
                                        </Badge>
                                    ))}
                                </div>
                            </div>
                            <div>
                                <p className="text-sm font-medium mb-2">Security Deposit</p>
                                <p className="text-lg font-semibold">
                                    {moveOutSummary.security_deposit > 0
                                        ? formatCurrency(moveOutSummary.security_deposit)
                                        : 'No deposit on file'}
                                </p>
                            </div>
                        </div>

                        {moveOutSummary.is_after_end_date && (
                            <>
                                <Separator className="my-4" />
                                <div className="rounded-lg bg-blue-50 border border-blue-200 p-3">
                                    <p className="text-sm text-blue-800">
                                        <strong>Note:</strong> The lease has already expired on {formatDate(moveOutSummary.end_date)}.
                                        This move-out will formally close the lease.
                                    </p>
                                </div>
                            </>
                        )}
                    </CardContent>
                </Card>

                {/* Move-Out Form */}
                <form onSubmit={handleSubmit}>
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <ClipboardCheck className="h-5 w-5" />
                                <CardTitle>Move-Out Details</CardTitle>
                            </div>
                            <CardDescription>Complete the move-out inspection and settlement</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="move_out_date">Move-Out Date *</Label>
                                    <Input
                                        id="move_out_date"
                                        type="date"
                                        value={data.move_out_date}
                                        onChange={(e) => setData('move_out_date', e.target.value)}
                                    />
                                    {errors.move_out_date && (
                                        <p className="text-sm text-destructive">{errors.move_out_date}</p>
                                    )}
                                    <p className="text-xs text-muted-foreground">
                                        The date the tenant vacated the premises
                                    </p>
                                </div>

                                {moveOutSummary.security_deposit > 0 && (
                                    <div className="space-y-2">
                                        <Label htmlFor="deposit_refund_amount">Deposit Refund Amount (SAR)</Label>
                                        <Input
                                            id="deposit_refund_amount"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max={moveOutSummary.security_deposit}
                                            value={data.deposit_refund_amount}
                                            onChange={(e) => setData('deposit_refund_amount', parseFloat(e.target.value) || 0)}
                                        />
                                        {errors.deposit_refund_amount && (
                                            <p className="text-sm text-destructive">{errors.deposit_refund_amount}</p>
                                        )}
                                        <p className="text-xs text-muted-foreground">
                                            Original deposit: {formatCurrency(moveOutSummary.security_deposit)}
                                        </p>
                                    </div>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="inspection_notes">Inspection Notes</Label>
                                <Textarea
                                    id="inspection_notes"
                                    placeholder="Document the condition of the unit upon move-out..."
                                    rows={4}
                                    value={data.inspection_notes}
                                    onChange={(e) => setData('inspection_notes', e.target.value)}
                                />
                                {errors.inspection_notes && (
                                    <p className="text-sm text-destructive">{errors.inspection_notes}</p>
                                )}
                                <p className="text-xs text-muted-foreground">
                                    Record any damages, cleaning status, or other observations
                                </p>
                            </div>

                            {moveOutSummary.security_deposit > 0 && (
                                <div className="space-y-2">
                                    <Label htmlFor="deposit_deductions">Deposit Deductions</Label>
                                    <Textarea
                                        id="deposit_deductions"
                                        placeholder="List any deductions from the security deposit (e.g., damages, cleaning fees, unpaid rent)..."
                                        rows={3}
                                        value={data.deposit_deductions}
                                        onChange={(e) => setData('deposit_deductions', e.target.value)}
                                    />
                                    {errors.deposit_deductions && (
                                        <p className="text-sm text-destructive">{errors.deposit_deductions}</p>
                                    )}
                                </div>
                            )}

                            {/* Settlement Summary */}
                            <div className="rounded-lg border p-4 bg-muted/50">
                                <h4 className="font-medium mb-3">Settlement Summary</h4>
                                <div className="space-y-2 text-sm">
                                    <div className="flex justify-between">
                                        <span>Security Deposit:</span>
                                        <span>{formatCurrency(moveOutSummary.security_deposit)}</span>
                                    </div>
                                    {moveOutSummary.security_deposit > 0 && (
                                        <>
                                            <div className="flex justify-between text-destructive">
                                                <span>Deductions:</span>
                                                <span>-{formatCurrency(moveOutSummary.security_deposit - data.deposit_refund_amount)}</span>
                                            </div>
                                            <Separator />
                                            <div className="flex justify-between font-medium">
                                                <span>Refund to Tenant:</span>
                                                <span className="text-green-600">{formatCurrency(data.deposit_refund_amount)}</span>
                                            </div>
                                        </>
                                    )}
                                </div>
                            </div>

                            <Separator />

                            <div className="flex justify-between">
                                <Link href={`/leases/${lease.id}`}>
                                    <Button type="button" variant="outline">
                                        Cancel
                                    </Button>
                                </Link>
                                <Button type="submit" disabled={processing}>
                                    {processing ? (
                                        <>Processing...</>
                                    ) : (
                                        <>
                                            <LogOut className="mr-2 h-4 w-4" />
                                            Complete Move-Out
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
