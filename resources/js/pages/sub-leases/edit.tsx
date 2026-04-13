import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Save } from 'lucide-react';

interface SubLease {
    id: number;
    contract_number: string | null;
    parent_lease_id: number | null;
    tenant_id: number;
    community_id: number | null;
    building_id: number | null;
    status_id: number;
    tenant_type: string;
    rental_type: string;
    rental_total_amount: string;
    security_deposit_amount: string | null;
    start_date: string;
    end_date: string;
    number_of_years: number;
    number_of_months: number;
    terms_conditions: string | null;
    units: { id: number; name: string }[];
}

interface ParentLeaseOption { id: number; label: string }
interface Option { id: number; name: string }
interface Status { id: number; name: string }

interface Props {
    sublease: SubLease;
    parentLeases: ParentLeaseOption[];
    communities: Option[];
    buildings: (Option & { community_id: number | null })[];
    units: (Option & { building_id: number | null })[];
    tenants: (Option & { email: string; phone: string })[];
    statuses: Status[];
}

export default function SubLeasesEdit({ sublease, parentLeases, communities, buildings, units, tenants, statuses }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        parent_lease_id: String(sublease.parent_lease_id ?? ''),
        tenant_id: String(sublease.tenant_id),
        community_id: String(sublease.community_id ?? ''),
        building_id: String(sublease.building_id ?? ''),
        status_id: String(sublease.status_id),
        contract_number: sublease.contract_number ?? '',
        tenant_type: sublease.tenant_type,
        rental_type: sublease.rental_type,
        rental_total_amount: sublease.rental_total_amount,
        security_deposit_amount: sublease.security_deposit_amount ?? '',
        start_date: sublease.start_date,
        end_date: sublease.end_date,
        number_of_years: String(sublease.number_of_years),
        number_of_months: String(sublease.number_of_months),
        terms_conditions: sublease.terms_conditions ?? '',
        units: sublease.units.map(u => ({ id: u.id })),
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(`/sub-leases/${sublease.id}`);
    };

    const toggleUnit = (unitId: number) => {
        const exists = data.units.some(u => u.id === unitId);
        setData('units', exists ? data.units.filter(u => u.id !== unitId) : [...data.units, { id: unitId }]);
    };

    return (
        <>
            <Head title={`Edit Sub-Lease ${sublease.contract_number ?? `#${sublease.id}`}`} />
            <div className="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center gap-4">
                    <Link href={`/sub-leases/${sublease.id}`}>
                        <Button variant="outline" size="sm"><ArrowLeft className="mr-2 h-4 w-4" />Back</Button>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Edit Sub-Lease</h1>
                        <p className="text-muted-foreground">{sublease.contract_number ?? `#${sublease.id}`}</p>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6 max-w-3xl">
                    {/* Parent Lease */}
                    <Card>
                        <CardHeader><CardTitle>Parent Lease</CardTitle></CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                <Label>Parent Lease *</Label>
                                <Select value={data.parent_lease_id} onValueChange={v => setData('parent_lease_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="Select parent lease" /></SelectTrigger>
                                    <SelectContent>
                                        {parentLeases.map(l => (
                                            <SelectItem key={l.id} value={String(l.id)}>{l.label}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.parent_lease_id && <p className="text-sm text-destructive">{errors.parent_lease_id}</p>}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Sub-Tenant & Contract */}
                    <Card>
                        <CardHeader><CardTitle>Sub-Tenant & Contract Details</CardTitle></CardHeader>
                        <CardContent className="grid gap-4 md:grid-cols-2">
                            <div className="space-y-2">
                                <Label>Sub-Tenant *</Label>
                                <Select value={data.tenant_id} onValueChange={v => setData('tenant_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="Select sub-tenant" /></SelectTrigger>
                                    <SelectContent>
                                        {tenants.map(t => (
                                            <SelectItem key={t.id} value={String(t.id)}>{t.name}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.tenant_id && <p className="text-sm text-destructive">{errors.tenant_id}</p>}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="contract_number">Contract Number *</Label>
                                <Input id="contract_number" value={data.contract_number} onChange={e => setData('contract_number', e.target.value)} />
                                {errors.contract_number && <p className="text-sm text-destructive">{errors.contract_number}</p>}
                            </div>
                            <div className="space-y-2">
                                <Label>Tenant Type *</Label>
                                <Select value={data.tenant_type} onValueChange={v => setData('tenant_type', v)}>
                                    <SelectTrigger><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="individual">Individual</SelectItem>
                                        <SelectItem value="corporate">Corporate</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2">
                                <Label>Status *</Label>
                                <Select value={data.status_id} onValueChange={v => setData('status_id', v)}>
                                    <SelectTrigger><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        {statuses.map(s => (
                                            <SelectItem key={s.id} value={String(s.id)}>{s.name}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.status_id && <p className="text-sm text-destructive">{errors.status_id}</p>}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Location */}
                    <Card>
                        <CardHeader><CardTitle>Location</CardTitle></CardHeader>
                        <CardContent className="grid gap-4 md:grid-cols-2">
                            <div className="space-y-2">
                                <Label>Community</Label>
                                <Select value={data.community_id} onValueChange={v => setData('community_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="Select community" /></SelectTrigger>
                                    <SelectContent>
                                        {communities.map(c => (
                                            <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2">
                                <Label>Building</Label>
                                <Select value={data.building_id} onValueChange={v => setData('building_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="Select building" /></SelectTrigger>
                                    <SelectContent>
                                        {buildings.map(b => (
                                            <SelectItem key={b.id} value={String(b.id)}>{b.name}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2 md:col-span-2">
                                <Label>Units</Label>
                                <div className="grid grid-cols-3 gap-2 max-h-40 overflow-y-auto border rounded-md p-2">
                                    {units.map(u => {
                                        const selected = data.units.some(su => su.id === u.id);
                                        return (
                                            <button
                                                key={u.id}
                                                type="button"
                                                onClick={() => toggleUnit(u.id)}
                                                className={`text-xs px-2 py-1 rounded border transition-colors ${selected ? 'bg-primary text-primary-foreground border-primary' : 'hover:bg-muted'}`}
                                            >
                                                {u.name}
                                            </button>
                                        );
                                    })}
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Financial & Dates */}
                    <Card>
                        <CardHeader><CardTitle>Financial & Duration</CardTitle></CardHeader>
                        <CardContent className="grid gap-4 md:grid-cols-2">
                            <div className="space-y-2">
                                <Label htmlFor="rental_total_amount">Total Rental Amount *</Label>
                                <Input id="rental_total_amount" type="number" step="0.01" min="0" value={data.rental_total_amount} onChange={e => setData('rental_total_amount', e.target.value)} />
                                {errors.rental_total_amount && <p className="text-sm text-destructive">{errors.rental_total_amount}</p>}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="security_deposit_amount">Security Deposit</Label>
                                <Input id="security_deposit_amount" type="number" step="0.01" min="0" value={data.security_deposit_amount} onChange={e => setData('security_deposit_amount', e.target.value)} />
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="start_date">Start Date *</Label>
                                <Input id="start_date" type="date" value={data.start_date} onChange={e => setData('start_date', e.target.value)} />
                                {errors.start_date && <p className="text-sm text-destructive">{errors.start_date}</p>}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="end_date">End Date *</Label>
                                <Input id="end_date" type="date" value={data.end_date} onChange={e => setData('end_date', e.target.value)} />
                                {errors.end_date && <p className="text-sm text-destructive">{errors.end_date}</p>}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="number_of_years">Duration (Years)</Label>
                                <Input id="number_of_years" type="number" min="0" value={data.number_of_years} onChange={e => setData('number_of_years', e.target.value)} />
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="number_of_months">Duration (Months)</Label>
                                <Input id="number_of_months" type="number" min="0" max="11" value={data.number_of_months} onChange={e => setData('number_of_months', e.target.value)} />
                            </div>
                        </CardContent>
                    </Card>

                    {/* Terms */}
                    <Card>
                        <CardHeader><CardTitle>Terms & Conditions</CardTitle></CardHeader>
                        <CardContent>
                            <Textarea value={data.terms_conditions} onChange={e => setData('terms_conditions', e.target.value)} rows={4} />
                        </CardContent>
                    </Card>

                    <div className="flex items-center gap-4">
                        <Button type="submit" disabled={processing}>
                            <Save className="mr-2 h-4 w-4" />
                            {processing ? 'Saving...' : 'Save Changes'}
                        </Button>
                        <Link href={`/sub-leases/${sublease.id}`}>
                            <Button variant="outline">Cancel</Button>
                        </Link>
                    </div>
                </form>
            </div>
        </>
    );
}

SubLeasesEdit.layout = {
    breadcrumbs: [
        { title: 'Leasing', href: '/leasing' },
        { title: 'Sub-Leases', href: '/sub-leases' },
        { title: 'Edit', href: '#' },
    ],
};
