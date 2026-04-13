import { Head, Link, router } from '@inertiajs/react';
import {
    FileText,
    Edit,
    Calendar,
    DollarSign,
    User,
    Building2,
    CheckCircle,
    Clock,
    XCircle,
    AlertCircle,
    RefreshCw,
    ArrowLeft,
    Home,
    Ban,
    LogOut,
} from 'lucide-react';
import { useState } from 'react';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface Unit {
    id: number;
    name: string;
    building?: {
        id: number;
        name: string;
    };
    pivot?: {
        annual_rental_amount: string;
        net_area: string;
        meter_cost: string;
    };
}

interface Contact {
    id: number;
    name: string;
    email?: string;
    phone?: string;
}

interface Status {
    id: number;
    name: string;
}

interface Community {
    id: number;
    name: string;
}

interface Building {
    id: number;
    name: string;
}

interface Transaction {
    id: number;
    type: string;
    amount: string;
    due_date: string;
    paid_date: string | null;
    status: string;
}

interface Lease {
    id: number;
    contract_number: string | null;
    tenant: Contact | null;
    units: Unit[];
    status: Status | null;
    community: Community | null;
    building: Building | null;
    start_date: string;
    end_date: string;
    handover_date: string | null;
    rental_total_amount: string;
    security_deposit_amount: string | null;
    security_deposit_due_date: string | null;
    tenant_type: string;
    rental_type: string;
    number_of_years: number;
    number_of_months: number;
    number_of_days: number;
    free_period: number | null;
    terms_conditions: string | null;
    is_renew: boolean;
    is_terms: boolean;
    created_by: Contact | null;
    deal_owner: Contact | null;
    transactions: Transaction[];
    created_at: string;
    updated_at: string;
}

interface LeaseShowProps {
    lease: Lease;
    canRenew: boolean;
    canTerminate: boolean;
}

function getStatusBadge(status: Status | null) {
    if (!status) {
return <Badge variant="secondary">Unknown</Badge>;
}

    const statusConfig: Record<number, { variant: 'default' | 'secondary' | 'destructive' | 'outline'; className: string; icon: React.ReactNode }> = {
        30: { variant: 'outline', className: 'border-blue-500 text-blue-600', icon: <Clock className="h-3 w-3" /> },
        31: { variant: 'default', className: 'bg-green-100 text-green-800', icon: <CheckCircle className="h-3 w-3" /> },
        32: { variant: 'secondary', className: 'bg-orange-100 text-orange-800', icon: <AlertCircle className="h-3 w-3" /> },
        33: { variant: 'destructive', className: 'bg-red-100 text-red-800', icon: <XCircle className="h-3 w-3" /> },
        34: { variant: 'secondary', className: 'bg-gray-100 text-gray-800', icon: <FileText className="h-3 w-3" /> },
    };

    const config = statusConfig[status.id] || { variant: 'secondary' as const, className: '', icon: null };

    return (
        <Badge variant={config.variant} className={config.className}>
            {config.icon}
            <span className="ml-1">{status.name}</span>
        </Badge>
    );
}

function formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function formatCurrency(amount: string | number): string {
    const num = typeof amount === 'string' ? parseFloat(amount) : amount;

    return `$${num.toLocaleString()}`;
}

export default function LeaseShow({ lease, canRenew, canTerminate }: LeaseShowProps) {
    const [isActivating, setIsActivating] = useState(false);

    const handleActivate = () => {
        setIsActivating(true);
        router.post(`/leases/${lease.id}/activate`, {}, {
            onFinish: () => setIsActivating(false),
        });
    };

    const isNew = lease.status?.id === 30;
    const isActive = lease.status?.id === 31;
    const isExpired = lease.status?.id === 32;

    return (
        <>
            <Head title={`Lease ${lease.contract_number || `#${lease.id}`}`} />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link href="/leases">
                            <Button variant="ghost" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back
                            </Button>
                        </Link>
                        <div>
                            <div className="flex items-center gap-3">
                                <h1 className="text-2xl font-bold tracking-tight">
                                    {lease.contract_number || `Lease #${lease.id}`}
                                </h1>
                                {getStatusBadge(lease.status)}
                                {lease.is_renew && (
                                    <Badge variant="outline" className="border-purple-500 text-purple-600">
                                        <RefreshCw className="mr-1 h-3 w-3" />
                                        Renewed
                                    </Badge>
                                )}
                            </div>
                            <p className="text-muted-foreground">
                                Created on {formatDate(lease.created_at)}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center gap-2">
                        {isNew && (
                            <AlertDialog>
                                <AlertDialogTrigger asChild>
                                    <Button variant="default">
                                        <CheckCircle className="mr-2 h-4 w-4" />
                                        Activate
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Activate Lease</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            This will activate the lease and mark the units as rented.
                                            Are you sure you want to proceed?
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction onClick={handleActivate} disabled={isActivating}>
                                            {isActivating ? 'Activating...' : 'Activate'}
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        )}

                        {canTerminate && (
                            <Link href={`/leases/${lease.id}/terminate`}>
                                <Button variant="destructive">
                                    <Ban className="mr-2 h-4 w-4" />
                                    Terminate
                                </Button>
                            </Link>
                        )}

                        {(isActive || isExpired) && (
                            <Link href={`/leases/${lease.id}/move-out`}>
                                <Button variant="outline">
                                    <LogOut className="mr-2 h-4 w-4" />
                                    Move Out
                                </Button>
                            </Link>
                        )}

                        {canRenew && (
                            <Link href={`/leases/${lease.id}/renew`}>
                                <Button variant="outline">
                                    <RefreshCw className="mr-2 h-4 w-4" />
                                    Renew
                                </Button>
                            </Link>
                        )}

                        <Link href={`/leases/${lease.id}/edit`}>
                            <Button variant="outline">
                                <Edit className="mr-2 h-4 w-4" />
                                Edit
                            </Button>
                        </Link>
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Main Content */}
                    <div className="space-y-6 lg:col-span-2">
                        {/* Lease Details */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <FileText className="h-5 w-5" />
                                    Lease Details
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <p className="text-sm text-muted-foreground">Contract Number</p>
                                        <p className="font-medium">{lease.contract_number || '-'}</p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">Tenant Type</p>
                                        <p className="font-medium capitalize">{lease.tenant_type}</p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">Rental Type</p>
                                        <p className="font-medium capitalize">{lease.rental_type}</p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">Duration</p>
                                        <p className="font-medium">
                                            {lease.number_of_years > 0 && `${lease.number_of_years} year${lease.number_of_years > 1 ? 's' : ''}`}
                                            {lease.number_of_months > 0 && ` ${lease.number_of_months} month${lease.number_of_months > 1 ? 's' : ''}`}
                                            {lease.number_of_days > 0 && ` ${lease.number_of_days} day${lease.number_of_days > 1 ? 's' : ''}`}
                                        </p>
                                    </div>
                                </div>

                                <Separator />

                                <div className="grid gap-4 sm:grid-cols-3">
                                    <div>
                                        <p className="text-sm text-muted-foreground">Start Date</p>
                                        <p className="font-medium">{formatDate(lease.start_date)}</p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">End Date</p>
                                        <p className="font-medium">{formatDate(lease.end_date)}</p>
                                    </div>
                                    {lease.handover_date && (
                                        <div>
                                            <p className="text-sm text-muted-foreground">Handover Date</p>
                                            <p className="font-medium">{formatDate(lease.handover_date)}</p>
                                        </div>
                                    )}
                                </div>

                                {lease.free_period && lease.free_period > 0 && (
                                    <>
                                        <Separator />
                                        <div>
                                            <p className="text-sm text-muted-foreground">Free Period</p>
                                            <p className="font-medium">{lease.free_period} days</p>
                                        </div>
                                    </>
                                )}
                            </CardContent>
                        </Card>

                        {/* Units */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Home className="h-5 w-5" />
                                    Leased Units
                                </CardTitle>
                                <CardDescription>
                                    {lease.units.length} unit{lease.units.length !== 1 ? 's' : ''} included in this lease
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Unit</TableHead>
                                            <TableHead>Building</TableHead>
                                            <TableHead>Area</TableHead>
                                            <TableHead className="text-right">Annual Rent</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {lease.units.map((unit) => (
                                            <TableRow key={unit.id}>
                                                <TableCell className="font-medium">{unit.name}</TableCell>
                                                <TableCell>{unit.building?.name || '-'}</TableCell>
                                                <TableCell>
                                                    {unit.pivot?.net_area ? `${unit.pivot.net_area} sqm` : '-'}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    {unit.pivot?.annual_rental_amount
                                                        ? formatCurrency(unit.pivot.annual_rental_amount)
                                                        : '-'}
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </CardContent>
                        </Card>

                        {/* Terms & Conditions */}
                        {lease.terms_conditions && (
                            <Card>
                                <CardHeader>
                                    <CardTitle>Terms & Conditions</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="whitespace-pre-wrap text-sm">{lease.terms_conditions}</p>
                                </CardContent>
                            </Card>
                        )}

                        {/* Transactions */}
                        {lease.transactions && lease.transactions.length > 0 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <DollarSign className="h-5 w-5" />
                                        Transactions
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Type</TableHead>
                                                <TableHead>Due Date</TableHead>
                                                <TableHead>Paid Date</TableHead>
                                                <TableHead>Status</TableHead>
                                                <TableHead className="text-right">Amount</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {lease.transactions.map((transaction) => (
                                                <TableRow key={transaction.id}>
                                                    <TableCell className="capitalize">{transaction.type}</TableCell>
                                                    <TableCell>{formatDate(transaction.due_date)}</TableCell>
                                                    <TableCell>
                                                        {transaction.paid_date ? formatDate(transaction.paid_date) : '-'}
                                                    </TableCell>
                                                    <TableCell>
                                                        <Badge variant={transaction.status === 'paid' ? 'default' : 'secondary'}>
                                                            {transaction.status}
                                                        </Badge>
                                                    </TableCell>
                                                    <TableCell className="text-right">
                                                        {formatCurrency(transaction.amount)}
                                                    </TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </CardContent>
                            </Card>
                        )}
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        {/* Financial Summary */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <DollarSign className="h-5 w-5" />
                                    Financial Summary
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <p className="text-sm text-muted-foreground">Total Rental Amount</p>
                                    <p className="text-2xl font-bold">{formatCurrency(lease.rental_total_amount)}</p>
                                </div>
                                {lease.security_deposit_amount && (
                                    <>
                                        <Separator />
                                        <div>
                                            <p className="text-sm text-muted-foreground">Security Deposit</p>
                                            <p className="text-lg font-semibold">{formatCurrency(lease.security_deposit_amount)}</p>
                                            {lease.security_deposit_due_date && (
                                                <p className="text-xs text-muted-foreground">
                                                    Due: {formatDate(lease.security_deposit_due_date)}
                                                </p>
                                            )}
                                        </div>
                                    </>
                                )}
                            </CardContent>
                        </Card>

                        {/* Tenant Info */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <User className="h-5 w-5" />
                                    Tenant
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                {lease.tenant ? (
                                    <div className="space-y-2">
                                        <p className="font-medium">{lease.tenant.name}</p>
                                        {lease.tenant.email && (
                                            <p className="text-sm text-muted-foreground">{lease.tenant.email}</p>
                                        )}
                                        {lease.tenant.phone && (
                                            <p className="text-sm text-muted-foreground">{lease.tenant.phone}</p>
                                        )}
                                        <Link href={`/contacts/${lease.tenant.id}`}>
                                            <Button variant="outline" size="sm" className="mt-2">
                                                View Profile
                                            </Button>
                                        </Link>
                                    </div>
                                ) : (
                                    <p className="text-muted-foreground">No tenant assigned</p>
                                )}
                            </CardContent>
                        </Card>

                        {/* Property Info */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Building2 className="h-5 w-5" />
                                    Property
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-2">
                                {lease.community && (
                                    <div>
                                        <p className="text-sm text-muted-foreground">Community</p>
                                        <p className="font-medium">{lease.community.name}</p>
                                    </div>
                                )}
                                {lease.building && (
                                    <div>
                                        <p className="text-sm text-muted-foreground">Building</p>
                                        <p className="font-medium">{lease.building.name}</p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        {/* Additional Info */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Calendar className="h-5 w-5" />
                                    Additional Info
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3 text-sm">
                                {lease.deal_owner && (
                                    <div>
                                        <p className="text-muted-foreground">Deal Owner</p>
                                        <p className="font-medium">{lease.deal_owner.name}</p>
                                    </div>
                                )}
                                {lease.created_by && (
                                    <div>
                                        <p className="text-muted-foreground">Created By</p>
                                        <p className="font-medium">{lease.created_by.name}</p>
                                    </div>
                                )}
                                <div>
                                    <p className="text-muted-foreground">Last Updated</p>
                                    <p className="font-medium">{formatDate(lease.updated_at)}</p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </>
    );
}

LeaseShow.layout = {
    breadcrumbs: [
        { title: 'Leases', href: '/leases' },
        { title: 'Details', href: '' },
    ],
};
