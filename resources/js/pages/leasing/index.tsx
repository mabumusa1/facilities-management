import { Head, Link } from '@inertiajs/react';
import { AlertCircle, ArrowRight, Calendar, CheckCircle, FileText, Layers, RefreshCw, Users } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

interface Lease {
    id: number;
    contract_number: string | null;
    tenant: { id: number; name: string } | null;
    status: { id: number; name: string } | null;
    units: { id: number; name: string }[];
    end_date: string;
}

interface Stats {
    leases: { total: number; active: number; expiring_soon: number };
    sub_leases: { total: number; active: number };
    applications: { total: number; pending: number };
    renewals: { total: number };
}

interface Props {
    stats: Stats;
    recentLeases: Lease[];
    expiringLeases: Lease[];
}

const modules = [
    {
        title: 'Leases',
        description: 'Manage main lease contracts, creation wizard, and lifecycle.',
        href: '/leases',
        icon: FileText,
        color: 'text-blue-600',
        bg: 'bg-blue-50',
    },
    {
        title: 'Sub-Leases',
        description: 'Sub-lease agreements linked to active parent leases.',
        href: '/sub-leases',
        icon: Layers,
        color: 'text-purple-600',
        bg: 'bg-purple-50',
    },
    {
        title: 'Applications',
        description: 'Lease applications, quotes, and approvals workflow.',
        href: '/lease-applications',
        icon: Users,
        color: 'text-green-600',
        bg: 'bg-green-50',
    },
    {
        title: 'Renewals',
        description: 'Track and manage lease renewal history.',
        href: '/leases?status=active',
        icon: RefreshCw,
        color: 'text-orange-600',
        bg: 'bg-orange-50',
    },
];

export default function LeasingIndex({ stats, recentLeases, expiringLeases }: Props) {
    return (
        <>
            <Head title="Leasing Module" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Leasing</h1>
                    <p className="text-muted-foreground">Overview of all leasing activity — leases, sub-leases, and applications</p>
                </div>

                {/* Summary Stats */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Leases</CardTitle>
                            <FileText className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.leases.total}</div>
                            <p className="text-xs text-muted-foreground">{stats.leases.active} active</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Sub-Leases</CardTitle>
                            <Layers className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.sub_leases.total}</div>
                            <p className="text-xs text-muted-foreground">{stats.sub_leases.active} active</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Applications</CardTitle>
                            <Users className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.applications.total}</div>
                            <p className="text-xs text-muted-foreground">{stats.applications.pending} pending</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Expiring Soon</CardTitle>
                            <AlertCircle className="h-4 w-4 text-orange-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-orange-600">{stats.leases.expiring_soon}</div>
                            <p className="text-xs text-muted-foreground">Within 30 days</p>
                        </CardContent>
                    </Card>
                </div>

                {/* Module Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    {modules.map(({ title, description, href, icon: Icon, color, bg }) => (
                        <Link key={title} href={href}>
                            <Card className="cursor-pointer hover:shadow-md transition-shadow h-full">
                                <CardContent className="p-6">
                                    <div className={`inline-flex p-3 rounded-lg ${bg} mb-4`}>
                                        <Icon className={`h-6 w-6 ${color}`} />
                                    </div>
                                    <h3 className="font-semibold mb-1">{title}</h3>
                                    <p className="text-sm text-muted-foreground">{description}</p>
                                    <div className={`flex items-center gap-1 mt-3 text-sm font-medium ${color}`}>
                                        View <ArrowRight className="h-3 w-3" />
                                    </div>
                                </CardContent>
                            </Card>
                        </Link>
                    ))}
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    {/* Recent Leases */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between">
                            <CardTitle className="text-base">Recent Leases</CardTitle>
                            <Link href="/leases">
                                <Button variant="ghost" size="sm">View all <ArrowRight className="ml-1 h-3 w-3" /></Button>
                            </Link>
                        </CardHeader>
                        <CardContent className="p-0">
                            {recentLeases.length === 0 ? (
                                <p className="text-sm text-muted-foreground p-6">No leases yet.</p>
                            ) : (
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Contract</TableHead>
                                            <TableHead>Tenant</TableHead>
                                            <TableHead>Status</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {recentLeases.map(lease => (
                                            <TableRow key={lease.id}>
                                                <TableCell>
                                                    <Link href={`/leases/${lease.id}`} className="text-blue-600 hover:underline text-sm font-medium">
                                                        {lease.contract_number ?? `#${lease.id}`}
                                                    </Link>
                                                </TableCell>
                                                <TableCell className="text-sm">{lease.tenant?.name ?? '—'}</TableCell>
                                                <TableCell>
                                                    {lease.status && (
                                                        <Badge variant="secondary" className="text-xs">{lease.status.name}</Badge>
                                                    )}
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            )}
                        </CardContent>
                    </Card>

                    {/* Expiring Leases */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between">
                            <CardTitle className="text-base flex items-center gap-2">
                                <AlertCircle className="h-4 w-4 text-orange-500" />
                                Expiring Within 30 Days
                            </CardTitle>
                            <Link href="/leases?status=active">
                                <Button variant="ghost" size="sm">View all <ArrowRight className="ml-1 h-3 w-3" /></Button>
                            </Link>
                        </CardHeader>
                        <CardContent className="p-0">
                            {expiringLeases.length === 0 ? (
                                <div className="flex flex-col items-center py-8 gap-2">
                                    <CheckCircle className="h-8 w-8 text-green-500" />
                                    <p className="text-sm text-muted-foreground">No leases expiring soon.</p>
                                </div>
                            ) : (
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Contract</TableHead>
                                            <TableHead>Tenant</TableHead>
                                            <TableHead>End Date</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {expiringLeases.map(lease => (
                                            <TableRow key={lease.id}>
                                                <TableCell>
                                                    <Link href={`/leases/${lease.id}`} className="text-blue-600 hover:underline text-sm font-medium">
                                                        {lease.contract_number ?? `#${lease.id}`}
                                                    </Link>
                                                </TableCell>
                                                <TableCell className="text-sm">{lease.tenant?.name ?? '—'}</TableCell>
                                                <TableCell>
                                                    <div className="flex items-center gap-1 text-sm text-orange-600">
                                                        <Calendar className="h-3 w-3" />
                                                        {new Date(lease.end_date).toLocaleDateString()}
                                                    </div>
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

LeasingIndex.layout = {
    breadcrumbs: [
        { title: 'Leasing', href: '/leasing' },
    ],
};
