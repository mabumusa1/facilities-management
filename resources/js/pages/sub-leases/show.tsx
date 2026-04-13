import { Head, Link, router } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { ArrowLeft, Building2, Calendar, DollarSign, Edit, FileText, Trash2, User } from 'lucide-react';

interface Status { id: number; name: string }
interface Contact { id: number; name: string; email?: string; phone?: string }
interface Unit { id: number; name: string }
interface Community { id: number; name: string }
interface Building { id: number; name: string }
interface ParentLease { id: number; contract_number: string | null; tenant: Contact | null }

interface SubLease {
    id: number;
    contract_number: string | null;
    tenant: Contact | null;
    parent_lease: ParentLease | null;
    units: Unit[];
    community: Community | null;
    building: Building | null;
    status: Status | null;
    tenant_type: string;
    rental_type: string;
    rental_total_amount: string;
    security_deposit_amount: string | null;
    start_date: string;
    end_date: string;
    number_of_years: number;
    number_of_months: number;
    terms_conditions: string | null;
    created_at: string;
}

interface Props { sublease: SubLease }

export default function SubLeasesShow({ sublease }: Props) {
    const handleDelete = () => {
        if (confirm('Are you sure you want to delete this sub-lease?')) {
            router.delete(`/sub-leases/${sublease.id}`);
        }
    };

    return (
        <>
            <Head title={`Sub-Lease ${sublease.contract_number ?? `#${sublease.id}`}`} />
            <div className="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link href="/sub-leases">
                            <Button variant="outline" size="sm"><ArrowLeft className="mr-2 h-4 w-4" />Back</Button>
                        </Link>
                        <div>
                            <h1 className="text-2xl font-bold tracking-tight">
                                Sub-Lease {sublease.contract_number ?? `#${sublease.id}`}
                            </h1>
                            {sublease.status && (
                                <Badge variant="outline" className="mt-1">{sublease.status.name}</Badge>
                            )}
                        </div>
                    </div>
                    <div className="flex items-center gap-2">
                        <Link href={`/sub-leases/${sublease.id}/edit`}>
                            <Button variant="outline" size="sm"><Edit className="mr-2 h-4 w-4" />Edit</Button>
                        </Link>
                        <Button variant="destructive" size="sm" onClick={handleDelete}>
                            <Trash2 className="mr-2 h-4 w-4" />Delete
                        </Button>
                    </div>
                </div>

                <div className="grid gap-6 md:grid-cols-2">
                    {/* Parent Lease */}
                    <Card>
                        <CardHeader><CardTitle className="flex items-center gap-2"><FileText className="h-4 w-4" />Parent Lease</CardTitle></CardHeader>
                        <CardContent>
                            {sublease.parent_lease ? (
                                <div className="space-y-2">
                                    <Link href={`/leases/${sublease.parent_lease.id}`} className="text-blue-600 hover:underline font-medium">
                                        {sublease.parent_lease.contract_number ?? `#${sublease.parent_lease.id}`}
                                    </Link>
                                    {sublease.parent_lease.tenant && (
                                        <p className="text-sm text-muted-foreground">Tenant: {sublease.parent_lease.tenant.name}</p>
                                    )}
                                </div>
                            ) : (
                                <p className="text-sm text-muted-foreground">No parent lease linked</p>
                            )}
                        </CardContent>
                    </Card>

                    {/* Sub-Tenant */}
                    <Card>
                        <CardHeader><CardTitle className="flex items-center gap-2"><User className="h-4 w-4" />Sub-Tenant</CardTitle></CardHeader>
                        <CardContent className="space-y-2">
                            <p className="font-medium">{sublease.tenant?.name ?? '—'}</p>
                            {sublease.tenant?.email && <p className="text-sm text-muted-foreground">{sublease.tenant.email}</p>}
                            {sublease.tenant?.phone && <p className="text-sm text-muted-foreground">{sublease.tenant.phone}</p>}
                            <Badge variant="secondary" className="capitalize">{sublease.tenant_type}</Badge>
                        </CardContent>
                    </Card>

                    {/* Location */}
                    <Card>
                        <CardHeader><CardTitle className="flex items-center gap-2"><Building2 className="h-4 w-4" />Location</CardTitle></CardHeader>
                        <CardContent className="space-y-2">
                            {sublease.community && <p className="text-sm"><span className="text-muted-foreground">Community:</span> {sublease.community.name}</p>}
                            {sublease.building && <p className="text-sm"><span className="text-muted-foreground">Building:</span> {sublease.building.name}</p>}
                            {sublease.units.length > 0 && (
                                <div className="flex flex-wrap gap-1 mt-2">
                                    {sublease.units.map(u => (
                                        <Badge key={u.id} variant="secondary">{u.name}</Badge>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Dates */}
                    <Card>
                        <CardHeader><CardTitle className="flex items-center gap-2"><Calendar className="h-4 w-4" />Duration</CardTitle></CardHeader>
                        <CardContent className="space-y-2">
                            <div className="grid grid-cols-2 gap-2 text-sm">
                                <div><span className="text-muted-foreground">Start:</span><p className="font-medium">{new Date(sublease.start_date).toLocaleDateString()}</p></div>
                                <div><span className="text-muted-foreground">End:</span><p className="font-medium">{new Date(sublease.end_date).toLocaleDateString()}</p></div>
                                <div><span className="text-muted-foreground">Years:</span><p className="font-medium">{sublease.number_of_years}</p></div>
                                <div><span className="text-muted-foreground">Months:</span><p className="font-medium">{sublease.number_of_months}</p></div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Financial */}
                    <Card>
                        <CardHeader><CardTitle className="flex items-center gap-2"><DollarSign className="h-4 w-4" />Financial</CardTitle></CardHeader>
                        <CardContent className="space-y-3">
                            <div className="flex items-center justify-between">
                                <span className="text-sm text-muted-foreground">Total Rental Amount</span>
                                <span className="font-bold text-lg">${parseFloat(sublease.rental_total_amount).toLocaleString()}</span>
                            </div>
                            {sublease.security_deposit_amount && (
                                <>
                                    <Separator />
                                    <div className="flex items-center justify-between">
                                        <span className="text-sm text-muted-foreground">Security Deposit</span>
                                        <span className="font-medium">${parseFloat(sublease.security_deposit_amount).toLocaleString()}</span>
                                    </div>
                                </>
                            )}
                        </CardContent>
                    </Card>

                    {/* Terms */}
                    {sublease.terms_conditions && (
                        <Card>
                            <CardHeader><CardTitle>Terms & Conditions</CardTitle></CardHeader>
                            <CardContent>
                                <p className="text-sm whitespace-pre-wrap">{sublease.terms_conditions}</p>
                            </CardContent>
                        </Card>
                    )}
                </div>
            </div>
        </>
    );
}

SubLeasesShow.layout = {
    breadcrumbs: [
        { title: 'Leasing', href: '/leasing' },
        { title: 'Sub-Leases', href: '/sub-leases' },
        { title: 'View', href: '#' },
    ],
};
