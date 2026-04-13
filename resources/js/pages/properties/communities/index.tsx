import { Head, Link, router } from '@inertiajs/react';
import { Building2, MapPin, Plus, Search } from 'lucide-react';
import { useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { index as communitiesIndex, create as communitiesCreate, show as communitiesShow } from '@/routes/communities';

interface Community {
    id: number;
    name: string;
    status: 'active' | 'inactive';
    city?: { id: number; name: string };
    district?: { id: number; name: string };
    buildings_count: number;
    created_at: string;
}

interface PaginatedData {
    data: Community[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

interface Props {
    communities: PaginatedData;
    filters: {
        search?: string;
        status?: string;
        sort?: string;
        direction?: string;
    };
}

export default function CommunitiesIndex({ communities, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status ?? '');

    const handleSearch = () => {
        router.get(communitiesIndex(), { search, status }, { preserveState: true });
    };

    const handleStatusChange = (value: string) => {
        setStatus(value);
        router.get(communitiesIndex(), { search, status: value }, { preserveState: true });
    };

    return (
        <>
            <Head title="Communities" />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Communities</h1>
                        <p className="text-muted-foreground">
                            Manage your property communities
                        </p>
                    </div>
                    <Button asChild>
                        <Link href={communitiesCreate()}>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Community
                        </Link>
                    </Button>
                </div>

                {/* Filters */}
                <Card>
                    <CardContent className="pt-6">
                        <div className="flex gap-4">
                            <div className="relative flex-1">
                                <Search className="text-muted-foreground absolute top-2.5 left-2.5 h-4 w-4" />
                                <Input
                                    placeholder="Search communities..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    onKeyDown={(e) => e.key === 'Enter' && handleSearch()}
                                    className="pl-8"
                                />
                            </div>
                            <Select value={status} onValueChange={handleStatusChange}>
                                <SelectTrigger className="w-40">
                                    <SelectValue placeholder="All statuses" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">All statuses</SelectItem>
                                    <SelectItem value="active">Active</SelectItem>
                                    <SelectItem value="inactive">Inactive</SelectItem>
                                </SelectContent>
                            </Select>
                            <Button onClick={handleSearch}>Search</Button>
                        </div>
                    </CardContent>
                </Card>

                {/* Data Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>
                            {communities.total} {communities.total === 1 ? 'Community' : 'Communities'}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {communities.data.length === 0 ? (
                            <div className="text-muted-foreground py-8 text-center">
                                No communities found. Create your first community to get started.
                            </div>
                        ) : (
                            <div className="divide-y">
                                {communities.data.map((community) => (
                                    <Link
                                        key={community.id}
                                        href={communitiesShow({ community: community.id })}
                                        className="hover:bg-muted/50 flex items-center justify-between p-4 transition-colors"
                                    >
                                        <div className="flex items-center gap-4">
                                            <div className="bg-primary/10 flex h-10 w-10 items-center justify-center rounded-lg">
                                                <Building2 className="text-primary h-5 w-5" />
                                            </div>
                                            <div>
                                                <div className="font-medium">{community.name}</div>
                                                <div className="text-muted-foreground flex items-center gap-1 text-sm">
                                                    <MapPin className="h-3 w-3" />
                                                    {community.city?.name ?? 'No city'}, {community.district?.name ?? 'No district'}
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4">
                                            <div className="text-muted-foreground text-sm">
                                                {community.buildings_count} buildings
                                            </div>
                                            <Badge variant={community.status === 'active' ? 'default' : 'secondary'}>
                                                {community.status}
                                            </Badge>
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        )}

                        {/* Pagination */}
                        {communities.last_page > 1 && (
                            <div className="mt-4 flex items-center justify-center gap-2">
                                {communities.links.map((link, index) => (
                                    <Button
                                        key={index}
                                        variant={link.active ? 'default' : 'outline'}
                                        size="sm"
                                        disabled={!link.url}
                                        onClick={() => link.url && router.get(link.url)}
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

CommunitiesIndex.layout = {
    breadcrumbs: [
        { title: 'Properties', href: communitiesIndex() },
        { title: 'Communities', href: communitiesIndex() },
    ],
};
