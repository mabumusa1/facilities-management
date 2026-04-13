import { Head, Link, router } from '@inertiajs/react';
import { AlertCircle, Calendar, CheckCircle, Clock, Eye, FileText, Plus, Search, XCircle } from 'lucide-react';
import { useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

interface Unit { id: number; name: string }
interface Contact { id: number; name: string }
interface Status { id: number; name: string }
interface ParentLease { id: number; contract_number: string | null }

interface SubLease {
    id: number;
    contract_number: string | null;
    tenant: Contact | null;
    units: Unit[];
    status: Status | null;
    start_date: string;
    end_date: string;
    rental_total_amount: string;
    parent_lease: ParentLease | null;
}

interface Statistics { total: number; active: number; new: number; expired: number }

interface Props {
    subleases: { data: SubLease[]; current_page: number; last_page: number; total: number };
    statistics: Statistics;
    filters: { status: string | null; search: string | null };
}

function StatusBadge({ status }: { status: Status | null }) {
    if (!status) {
return <Badge variant="secondary">Unknown</Badge>;
}

    const map: Record<number, { className: string; icon: React.ReactNode }> = {
        30: { className: 'border-blue-500 text-blue-600', icon: <Clock className="h-3 w-3" /> },
        31: { className: 'bg-green-100 text-green-800', icon: <CheckCircle className="h-3 w-3" /> },
        32: { className: 'bg-orange-100 text-orange-800', icon: <AlertCircle className="h-3 w-3" /> },
        33: { className: 'bg-red-100 text-red-800', icon: <XCircle className="h-3 w-3" /> },
        34: { className: 'bg-gray-100 text-gray-800', icon: <FileText className="h-3 w-3" /> },
    };
    const cfg = map[status.id] ?? { className: '', icon: null };

    return (
        <Badge variant="outline" className={cfg.className}>
            {cfg.icon}
            <span className="ml-1">{status.name}</span>
        </Badge>
    );
}

export default function SubLeasesIndex({ subleases, statistics, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get('/sub-leases', { status: filters.status, search: search || null }, { preserveState: true });
    };

    const handleStatus = (val: string) => {
        router.get('/sub-leases', { status: val === 'all' ? null : val, search: filters.search }, { preserveState: true });
    };

    return (
        <>
            <Head title="Sub-Leases" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Sub-Leases</h1>
                        <p className="text-muted-foreground">Manage sub-lease agreements linked to parent leases</p>
                    </div>
                    <Link href="/sub-leases/create">
                        <Button><Plus className="mr-2 h-4 w-4" />New Sub-Lease</Button>
                    </Link>
                </div>

                {/* Statistics */}
                <div className="grid gap-4 md:grid-cols-4">
                    {[
                        { label: 'Total', value: statistics.total, icon: FileText, color: '' },
                        { label: 'Active', value: statistics.active, icon: CheckCircle, color: 'text-green-600' },
                        { label: 'New', value: statistics.new, icon: Clock, color: 'text-blue-600' },
                        { label: 'Expired', value: statistics.expired, icon: AlertCircle, color: 'text-orange-600' },
                    ].map(({ label, value, icon: Icon, color }) => (
                        <Card key={label}>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">{label}</CardTitle>
                                <Icon className={`h-4 w-4 text-muted-foreground ${color}`} />
                            </CardHeader>
                            <CardContent>
                                <div className={`text-2xl font-bold ${color}`}>{value}</div>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                {/* Filters */}
                <div className="flex items-center gap-4">
                    <form onSubmit={handleSearch} className="flex items-center gap-2">
                        <div className="relative">
                            <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input
                                placeholder="Search sub-leases..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="pl-8 w-[250px]"
                            />
                        </div>
                        <Button type="submit" variant="secondary" size="sm">Search</Button>
                    </form>
                    <Select value={filters.status ?? 'all'} onValueChange={handleStatus}>
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Filter by status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Statuses</SelectItem>
                            <SelectItem value="new">New</SelectItem>
                            <SelectItem value="active">Active</SelectItem>
                            <SelectItem value="expired">Expired</SelectItem>
                            <SelectItem value="cancelled">Cancelled</SelectItem>
                            <SelectItem value="closed">Closed</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                {/* Table */}
                {subleases.data.length === 0 ? (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <FileText className="h-12 w-12 text-muted-foreground/50" />
                            <h3 className="mt-4 text-lg font-medium">No sub-leases found</h3>
                            <p className="mt-2 text-sm text-muted-foreground">Create your first sub-lease to get started.</p>
                            <Link href="/sub-leases/create" className="mt-4">
                                <Button><Plus className="mr-2 h-4 w-4" />Create Sub-Lease</Button>
                            </Link>
                        </CardContent>
                    </Card>
                ) : (
                    <Card>
                        <CardContent className="p-0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Contract #</TableHead>
                                        <TableHead>Parent Lease</TableHead>
                                        <TableHead>Sub-Tenant</TableHead>
                                        <TableHead>Units</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Period</TableHead>
                                        <TableHead>Amount</TableHead>
                                        <TableHead className="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {subleases.data.map((sl) => (
                                        <TableRow key={sl.id}>
                                            <TableCell className="font-medium">{sl.contract_number ?? `#${sl.id}`}</TableCell>
                                            <TableCell>
                                                {sl.parent_lease ? (
                                                    <Link href={`/leases/${sl.parent_lease.id}`} className="text-blue-600 hover:underline text-sm">
                                                        {sl.parent_lease.contract_number ?? `#${sl.parent_lease.id}`}
                                                    </Link>
                                                ) : '—'}
                                            </TableCell>
                                            <TableCell>{sl.tenant?.name ?? '—'}</TableCell>
                                            <TableCell>
                                                <div className="flex flex-wrap gap-1">
                                                    {sl.units.slice(0, 2).map(u => (
                                                        <Badge key={u.id} variant="secondary" className="text-xs">{u.name}</Badge>
                                                    ))}
                                                    {sl.units.length > 2 && (
                                                        <Badge variant="secondary" className="text-xs">+{sl.units.length - 2}</Badge>
                                                    )}
                                                </div>
                                            </TableCell>
                                            <TableCell><StatusBadge status={sl.status} /></TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-1 text-sm">
                                                    <Calendar className="h-3 w-3" />
                                                    {new Date(sl.start_date).toLocaleDateString()} – {new Date(sl.end_date).toLocaleDateString()}
                                                </div>
                                            </TableCell>
                                            <TableCell>${parseFloat(sl.rental_total_amount).toLocaleString()}</TableCell>
                                            <TableCell className="text-right">
                                                <Link href={`/sub-leases/${sl.id}`}>
                                                    <Button variant="ghost" size="sm"><Eye className="h-4 w-4" /></Button>
                                                </Link>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                )}

                {/* Pagination */}
                {subleases.last_page > 1 && (
                    <div className="flex items-center justify-center gap-2">
                        {Array.from({ length: subleases.last_page }, (_, i) => i + 1).map((page) => (
                            <Button
                                key={page}
                                variant={page === subleases.current_page ? 'default' : 'outline'}
                                size="sm"
                                onClick={() => router.get('/sub-leases', { page, status: filters.status, search: filters.search })}
                            >
                                {page}
                            </Button>
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}

SubLeasesIndex.layout = {
    breadcrumbs: [
        { title: 'Leasing', href: '/leasing' },
        { title: 'Sub-Leases', href: '/sub-leases' },
    ],
};
