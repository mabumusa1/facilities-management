import { Head, Link, useForm, router } from '@inertiajs/react';
import { FormEvent, useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, ArrowRight, FileText, RefreshCw, CalendarDays, DollarSign, Building2, User } from 'lucide-react';
import { type BreadcrumbItem } from '@/types';

interface Unit {
    id: number;
    name: string;
    building_id: number;
    pivot?: {
        rental_annual_type: string | null;
        annual_rental_amount: number | null;
        net_area: number | null;
        meter_cost: number | null;
    };
}

interface Lease {
    id: number;
    contract_number: string;
    tenant_id: number;
    community_id: number | null;
    building_id: number | null;
    tenant_type: string;
    rental_type: string | null;
    rental_total_amount: number;
    start_date: string;
    end_date: string;
    status_id: number;
    units: Unit[];
    tenant?: {
        id: number;
        name: string;
        email: string;
    };
    community?: {
        id: number;
        name: string;
    };
    building?: {
        id: number;
        name: string;
    };
    status?: {
        id: number;
        name: string;
    };
}

interface RenewalDefaults {
    original_lease_id: number;
    tenant_id: number;
    community_id: number | null;
    building_id: number | null;
    tenant_type: string;
    rental_type: string | null;
    payment_schedule_id: number | null;
    rental_contract_type_id: number | null;
    lease_unit_type_id: number | null;
    rental_total_amount: number;
    security_deposit_amount: number | null;
    units: Array<{
        id: number;
        rental_annual_type: string | null;
        annual_rental_amount: number | null;
        net_area: number | null;
        meter_cost: number | null;
    }>;
    start_date: string;
    end_date: string;
}

interface Props {
    originalLease: Lease;
    renewalDefaults: RenewalDefaults;
    communities: Array<{ id: number; name: string }>;
    buildings: Array<{ id: number; name: string; community_id: number }>;
    units: Array<{ id: number; name: string; building_id: number }>;
    tenants: Array<{ id: number; name: string; email: string; phone: string }>;
    statuses: Array<{ id: number; name: string }>;
}

export default function LeaseRenew({
    originalLease,
    renewalDefaults,
    communities,
    buildings,
    units,
    tenants,
    statuses,
}: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Leases', href: '/leases' },
        { title: originalLease.contract_number, href: `/leases/${originalLease.id}` },
        { title: 'Renew', href: '#' },
    ];

    const { data, setData, post, processing, errors } = useForm({
        start_date: renewalDefaults.start_date,
        end_date: renewalDefaults.end_date,
        rental_total_amount: renewalDefaults.rental_total_amount,
        rental_type: renewalDefaults.rental_type || 'detailed',
        units: renewalDefaults.units.map(u => ({
            id: u.id,
            rental_annual_type: u.rental_annual_type || 'total',
            annual_rental_amount: u.annual_rental_amount || 0,
            net_area: u.net_area || 0,
            meter_cost: u.meter_cost || 0,
        })),
    });

    const [step, setStep] = useState(1);

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post(`/leases/${originalLease.id}/renew`);
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

    const updateUnit = (index: number, field: string, value: string | number) => {
        const newUnits = [...data.units];
        newUnits[index] = { ...newUnits[index], [field]: value };
        setData('units', newUnits);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Renew Lease - ${originalLease.contract_number}`} />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-2xl font-bold tracking-tight">Renew Lease</h2>
                        <p className="text-muted-foreground">
                            Create a new lease from {originalLease.contract_number}
                        </p>
                    </div>
                    <Link href={`/leases/${originalLease.id}`}>
                        <Button variant="outline">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Lease
                        </Button>
                    </Link>
                </div>

                {/* Original Lease Summary */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-2">
                            <FileText className="h-5 w-5" />
                            <CardTitle>Original Lease Summary</CardTitle>
                        </div>
                        <CardDescription>Review the original lease details before renewal</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid gap-4 md:grid-cols-4">
                            <div className="flex items-center gap-3">
                                <div className="rounded-full bg-primary/10 p-2">
                                    <FileText className="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Contract</p>
                                    <p className="font-medium">{originalLease.contract_number}</p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="rounded-full bg-primary/10 p-2">
                                    <User className="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Tenant</p>
                                    <p className="font-medium">{originalLease.tenant?.name || 'N/A'}</p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="rounded-full bg-primary/10 p-2">
                                    <CalendarDays className="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Period</p>
                                    <p className="font-medium">
                                        {formatDate(originalLease.start_date)} - {formatDate(originalLease.end_date)}
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="rounded-full bg-primary/10 p-2">
                                    <DollarSign className="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Rental Amount</p>
                                    <p className="font-medium">{formatCurrency(originalLease.rental_total_amount)}</p>
                                </div>
                            </div>
                        </div>

                        <Separator className="my-4" />

                        <div>
                            <p className="text-sm font-medium mb-2">Units ({originalLease.units.length})</p>
                            <div className="flex flex-wrap gap-2">
                                {originalLease.units.map((unit) => (
                                    <Badge key={unit.id} variant="secondary">
                                        <Building2 className="mr-1 h-3 w-3" />
                                        {unit.name}
                                    </Badge>
                                ))}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Renewal Form */}
                <form onSubmit={handleSubmit}>
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <RefreshCw className="h-5 w-5" />
                                <CardTitle>New Lease Details</CardTitle>
                            </div>
                            <CardDescription>Configure the renewed lease terms</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            {/* Step 1: Dates and Amount */}
                            {step === 1 && (
                                <div className="space-y-6">
                                    <div className="grid gap-4 md:grid-cols-2">
                                        <div className="space-y-2">
                                            <Label htmlFor="start_date">Start Date *</Label>
                                            <Input
                                                id="start_date"
                                                type="date"
                                                value={data.start_date}
                                                onChange={(e) => setData('start_date', e.target.value)}
                                            />
                                            {errors.start_date && (
                                                <p className="text-sm text-destructive">{errors.start_date}</p>
                                            )}
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="end_date">End Date *</Label>
                                            <Input
                                                id="end_date"
                                                type="date"
                                                value={data.end_date}
                                                onChange={(e) => setData('end_date', e.target.value)}
                                            />
                                            {errors.end_date && (
                                                <p className="text-sm text-destructive">{errors.end_date}</p>
                                            )}
                                        </div>
                                    </div>

                                    <div className="grid gap-4 md:grid-cols-2">
                                        <div className="space-y-2">
                                            <Label htmlFor="rental_total_amount">Total Rental Amount (SAR) *</Label>
                                            <Input
                                                id="rental_total_amount"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                value={data.rental_total_amount}
                                                onChange={(e) => setData('rental_total_amount', parseFloat(e.target.value) || 0)}
                                            />
                                            {errors.rental_total_amount && (
                                                <p className="text-sm text-destructive">{errors.rental_total_amount}</p>
                                            )}
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="rental_type">Rental Type</Label>
                                            <Select
                                                value={data.rental_type}
                                                onValueChange={(value) => setData('rental_type', value)}
                                            >
                                                <SelectTrigger id="rental_type">
                                                    <SelectValue placeholder="Select type" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="summary">Summary</SelectItem>
                                                    <SelectItem value="detailed">Detailed</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                    </div>

                                    <div className="flex justify-end">
                                        <Button type="button" onClick={() => setStep(2)}>
                                            Next: Unit Details
                                            <ArrowRight className="ml-2 h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            )}

                            {/* Step 2: Unit Details */}
                            {step === 2 && (
                                <div className="space-y-6">
                                    <div>
                                        <h3 className="text-lg font-medium mb-4">Unit Rental Details</h3>
                                        <div className="space-y-4">
                                            {data.units.map((unit, index) => {
                                                const unitInfo = originalLease.units.find(u => u.id === unit.id);
                                                return (
                                                    <Card key={unit.id}>
                                                        <CardContent className="pt-4">
                                                            <div className="flex items-center gap-2 mb-4">
                                                                <Building2 className="h-4 w-4" />
                                                                <span className="font-medium">{unitInfo?.name || `Unit ${unit.id}`}</span>
                                                            </div>
                                                            <div className="grid gap-4 md:grid-cols-4">
                                                                <div className="space-y-2">
                                                                    <Label>Rental Type</Label>
                                                                    <Select
                                                                        value={unit.rental_annual_type}
                                                                        onValueChange={(value) => updateUnit(index, 'rental_annual_type', value)}
                                                                    >
                                                                        <SelectTrigger>
                                                                            <SelectValue placeholder="Select" />
                                                                        </SelectTrigger>
                                                                        <SelectContent>
                                                                            <SelectItem value="total">Total</SelectItem>
                                                                            <SelectItem value="per_meter">Per Meter</SelectItem>
                                                                        </SelectContent>
                                                                    </Select>
                                                                </div>
                                                                <div className="space-y-2">
                                                                    <Label>Annual Amount (SAR)</Label>
                                                                    <Input
                                                                        type="number"
                                                                        step="0.01"
                                                                        min="0"
                                                                        value={unit.annual_rental_amount}
                                                                        onChange={(e) => updateUnit(index, 'annual_rental_amount', parseFloat(e.target.value) || 0)}
                                                                    />
                                                                </div>
                                                                <div className="space-y-2">
                                                                    <Label>Net Area (sqm)</Label>
                                                                    <Input
                                                                        type="number"
                                                                        step="0.01"
                                                                        min="0"
                                                                        value={unit.net_area}
                                                                        onChange={(e) => updateUnit(index, 'net_area', parseFloat(e.target.value) || 0)}
                                                                    />
                                                                </div>
                                                                <div className="space-y-2">
                                                                    <Label>Meter Cost (SAR)</Label>
                                                                    <Input
                                                                        type="number"
                                                                        step="0.01"
                                                                        min="0"
                                                                        value={unit.meter_cost}
                                                                        onChange={(e) => updateUnit(index, 'meter_cost', parseFloat(e.target.value) || 0)}
                                                                    />
                                                                </div>
                                                            </div>
                                                        </CardContent>
                                                    </Card>
                                                );
                                            })}
                                        </div>
                                    </div>

                                    <div className="flex justify-between">
                                        <Button type="button" variant="outline" onClick={() => setStep(1)}>
                                            <ArrowLeft className="mr-2 h-4 w-4" />
                                            Back
                                        </Button>
                                        <Button type="button" onClick={() => setStep(3)}>
                                            Next: Review
                                            <ArrowRight className="ml-2 h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            )}

                            {/* Step 3: Review and Submit */}
                            {step === 3 && (
                                <div className="space-y-6">
                                    <div>
                                        <h3 className="text-lg font-medium mb-4">Review Renewal Details</h3>

                                        <div className="rounded-lg border p-4 space-y-4">
                                            <div className="grid gap-4 md:grid-cols-2">
                                                <div>
                                                    <p className="text-sm text-muted-foreground">Lease Period</p>
                                                    <p className="font-medium">
                                                        {formatDate(data.start_date)} - {formatDate(data.end_date)}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p className="text-sm text-muted-foreground">Total Rental Amount</p>
                                                    <p className="font-medium">{formatCurrency(data.rental_total_amount)}</p>
                                                </div>
                                            </div>

                                            <Separator />

                                            <div>
                                                <p className="text-sm text-muted-foreground mb-2">Units</p>
                                                <div className="space-y-2">
                                                    {data.units.map((unit) => {
                                                        const unitInfo = originalLease.units.find(u => u.id === unit.id);
                                                        return (
                                                            <div key={unit.id} className="flex justify-between items-center text-sm">
                                                                <span>{unitInfo?.name || `Unit ${unit.id}`}</span>
                                                                <span className="font-medium">{formatCurrency(unit.annual_rental_amount)}/year</span>
                                                            </div>
                                                        );
                                                    })}
                                                </div>
                                            </div>
                                        </div>

                                        <div className="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4">
                                            <p className="text-sm text-blue-800">
                                                <strong>Note:</strong> Creating this renewal will:
                                            </p>
                                            <ul className="text-sm text-blue-800 list-disc list-inside mt-2">
                                                <li>Create a new lease linked to the original ({originalLease.contract_number})</li>
                                                <li>Mark the original lease as renewed</li>
                                                <li>Keep the same tenant ({originalLease.tenant?.name})</li>
                                                <li>Retain the units with updated rental terms</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div className="flex justify-between">
                                        <Button type="button" variant="outline" onClick={() => setStep(2)}>
                                            <ArrowLeft className="mr-2 h-4 w-4" />
                                            Back
                                        </Button>
                                        <Button type="submit" disabled={processing}>
                                            {processing ? (
                                                <>
                                                    <RefreshCw className="mr-2 h-4 w-4 animate-spin" />
                                                    Processing...
                                                </>
                                            ) : (
                                                <>
                                                    <RefreshCw className="mr-2 h-4 w-4" />
                                                    Create Renewed Lease
                                                </>
                                            )}
                                        </Button>
                                    </div>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </form>
            </div>
        </AppLayout>
    );
}
