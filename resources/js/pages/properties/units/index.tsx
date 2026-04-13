import { Head, Link, router } from '@inertiajs/react';
import { Home, Plus, Search } from 'lucide-react';
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
import { index as unitsIndex, create as unitsCreate, show as unitsShow } from '@/routes/units';

interface Community {
    id: number;
    name: string;
}

interface Building {
    id: number;
    name: string;
}

interface UnitCategory {
    id: number;
    name: string;
}

interface Unit {
    id: number;
    name: string;
    status: 'active' | 'inactive';
    floor_no?: number;
    net_area?: number;
    market_rent?: number;
    community?: Community;
    building?: Building;
    category?: UnitCategory;
    created_at: string;
}

interface PaginatedData {
    data: Unit[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

interface Props {
    units: PaginatedData;
    communities: Community[];
    buildings: Building[];
    categories: UnitCategory[];
    filters: {
        search?: string;
        status?: string;
        community_id?: string;
        building_id?: string;
        category_id?: string;
    };
}

export default function UnitsIndex({ units, communities, buildings, categories, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status ?? '');
    const [communityId, setCommunityId] = useState(filters.community_id ?? '');
    const [buildingId, setBuildingId] = useState(filters.building_id ?? '');
    const [categoryId, setCategoryId] = useState(filters.category_id ?? '');

    const handleSearch = () => {
        router.get(unitsIndex(), {
            search,
            status,
            community_id: communityId,
            building_id: buildingId,
            category_id: categoryId,
        }, { preserveState: true });
    };

    const handleFilterChange = (key: string, value: string) => {
        const newFilters = {
            search,
            status,
            community_id: communityId,
            building_id: buildingId,
            category_id: categoryId,
            [key]: value,
        };
        if (key === 'status') setStatus(value);
        if (key === 'community_id') setCommunityId(value);
        if (key === 'building_id') setBuildingId(value);
        if (key === 'category_id') setCategoryId(value);
        router.get(unitsIndex(), newFilters, { preserveState: true });
    };

    return (
        <>
            <Head title="Units" />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Units</h1>
                        <p className="text-muted-foreground">Manage your property units</p>
                    </div>
                    <Button asChild>
                        <Link href={unitsCreate()}>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Unit
                        </Link>
                    </Button>
                </div>

                {/* Filters */}
                <Card>
                    <CardContent className="pt-6">
                        <div className="flex flex-wrap gap-4">
                            <div className="relative min-w-64 flex-1">
                                <Search className="text-muted-foreground absolute top-2.5 left-2.5 h-4 w-4" />
                                <Input
                                    placeholder="Search units..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    onKeyDown={(e) => e.key === 'Enter' && handleSearch()}
                                    className="pl-8"
                                />
                            </div>
                            <Select value={communityId} onValueChange={(v) => handleFilterChange('community_id', v)}>
                                <SelectTrigger className="w-40">
                                    <SelectValue placeholder="Community" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">All communities</SelectItem>
                                    {communities.map((c) => (
                                        <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <Select value={buildingId} onValueChange={(v) => handleFilterChange('building_id', v)}>
                                <SelectTrigger className="w-40">
                                    <SelectValue placeholder="Building" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">All buildings</SelectItem>
                                    {buildings.map((b) => (
                                        <SelectItem key={b.id} value={String(b.id)}>{b.name}</SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <Select value={categoryId} onValueChange={(v) => handleFilterChange('category_id', v)}>
                                <SelectTrigger className="w-40">
                                    <SelectValue placeholder="Category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">All categories</SelectItem>
                                    {categories.map((cat) => (
                                        <SelectItem key={cat.id} value={String(cat.id)}>{cat.name}</SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <Select value={status} onValueChange={(v) => handleFilterChange('status', v)}>
                                <SelectTrigger className="w-32">
                                    <SelectValue placeholder="Status" />
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
                            {units.total} {units.total === 1 ? 'Unit' : 'Units'}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {units.data.length === 0 ? (
                            <div className="text-muted-foreground py-8 text-center">
                                No units found. Create your first unit to get started.
                            </div>
                        ) : (
                            <div className="divide-y">
                                {units.data.map((unit) => (
                                    <Link
                                        key={unit.id}
                                        href={unitsShow({ unit: unit.id })}
                                        className="hover:bg-muted/50 flex items-center justify-between p-4 transition-colors"
                                    >
                                        <div className="flex items-center gap-4">
                                            <div className="bg-primary/10 flex h-10 w-10 items-center justify-center rounded-lg">
                                                <Home className="text-primary h-5 w-5" />
                                            </div>
                                            <div>
                                                <div className="font-medium">{unit.name}</div>
                                                <div className="text-muted-foreground text-sm">
                                                    {unit.building?.name ?? unit.community?.name ?? 'No location'}
                                                    {unit.floor_no !== undefined && ` • Floor ${unit.floor_no}`}
                                                    {unit.net_area && ` • ${unit.net_area} sqm`}
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4">
                                            {unit.category && (
                                                <Badge variant="outline">{unit.category.name}</Badge>
                                            )}
                                            {unit.market_rent && (
                                                <div className="text-muted-foreground text-sm">
                                                    ${unit.market_rent.toLocaleString()}/mo
                                                </div>
                                            )}
                                            <Badge variant={unit.status === 'active' ? 'default' : 'secondary'}>
                                                {unit.status}
                                            </Badge>
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        )}

                        {/* Pagination */}
                        {units.last_page > 1 && (
                            <div className="mt-4 flex items-center justify-center gap-2">
                                {units.links.map((link, index) => (
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

UnitsIndex.layout = {
    breadcrumbs: [
        { title: 'Properties', href: unitsIndex() },
        { title: 'Units', href: unitsIndex() },
    ],
};
