import { Head, Link, router } from "@inertiajs/react";
import { Building2, Plus, Search } from "lucide-react";
import { useState } from "react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    index as buildingsIndex,
    create as buildingsCreate,
    show as buildingsShow,
} from "@/routes/buildings";

interface Community {
    id: number;
    name: string;
}

interface Building {
    id: number;
    name: string;
    status: "active" | "inactive";
    no_floors: number;
    year_built?: number;
    community?: Community;
    units_count: number;
    created_at: string;
}

interface PaginatedData {
    data: Building[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

interface Props {
    buildings: PaginatedData;
    communities: Community[];
    filters: {
        search?: string;
        status?: string;
        community_id?: string;
        sort?: string;
        direction?: string;
    };
}

export default function BuildingsIndex({
    buildings,
    communities,
    filters,
}: Props) {
    const [search, setSearch] = useState(filters.search ?? "");
    const [status, setStatus] = useState(filters.status ?? "");
    const [communityId, setCommunityId] = useState(filters.community_id ?? "");

    const handleSearch = () => {
        router.get(
            buildingsIndex(),
            { search, status, community_id: communityId },
            { preserveState: true },
        );
    };

    const handleFilterChange = (key: string, value: string) => {
        const newFilters = {
            search,
            status,
            community_id: communityId,
            [key]: value,
        };

        if (key === "status") {
            setStatus(value);
        }

        if (key === "community_id") {
            setCommunityId(value);
        }

        router.get(buildingsIndex(), newFilters, { preserveState: true });
    };

    return (
        <>
            <Head title="Buildings" />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Buildings</h1>
                        <p className="text-muted-foreground">
                            Manage your property buildings
                        </p>
                    </div>
                    <Button asChild>
                        <Link href={buildingsCreate()}>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Building
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
                                    placeholder="Search buildings..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    onKeyDown={(e) =>
                                        e.key === "Enter" && handleSearch()
                                    }
                                    className="pl-8"
                                />
                            </div>
                            <Select
                                value={communityId}
                                onValueChange={(v) =>
                                    handleFilterChange("community_id", v)
                                }
                            >
                                <SelectTrigger className="w-48">
                                    <SelectValue placeholder="All communities" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">
                                        All communities
                                    </SelectItem>
                                    {communities.map((c) => (
                                        <SelectItem
                                            key={c.id}
                                            value={String(c.id)}
                                        >
                                            {c.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <Select
                                value={status}
                                onValueChange={(v) =>
                                    handleFilterChange("status", v)
                                }
                            >
                                <SelectTrigger className="w-40">
                                    <SelectValue placeholder="All statuses" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">
                                        All statuses
                                    </SelectItem>
                                    <SelectItem value="active">
                                        Active
                                    </SelectItem>
                                    <SelectItem value="inactive">
                                        Inactive
                                    </SelectItem>
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
                            {buildings.total}{" "}
                            {buildings.total === 1 ? "Building" : "Buildings"}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {buildings.data.length === 0 ? (
                            <div className="text-muted-foreground py-8 text-center">
                                No buildings found. Create your first building
                                to get started.
                            </div>
                        ) : (
                            <div className="divide-y">
                                {buildings.data.map((building) => (
                                    <Link
                                        key={building.id}
                                        href={buildingsShow({
                                            building: building.id,
                                        })}
                                        className="hover:bg-muted/50 flex items-center justify-between p-4 transition-colors"
                                    >
                                        <div className="flex items-center gap-4">
                                            <div className="bg-primary/10 flex h-10 w-10 items-center justify-center rounded-lg">
                                                <Building2 className="text-primary h-5 w-5" />
                                            </div>
                                            <div>
                                                <div className="font-medium">
                                                    {building.name}
                                                </div>
                                                <div className="text-muted-foreground text-sm">
                                                    {building.community?.name ??
                                                        "No community"}{" "}
                                                    &bull; {building.no_floors}{" "}
                                                    floors
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4">
                                            <div className="text-muted-foreground text-sm">
                                                {building.units_count} units
                                            </div>
                                            <Badge
                                                variant={
                                                    building.status === "active"
                                                        ? "default"
                                                        : "secondary"
                                                }
                                            >
                                                {building.status}
                                            </Badge>
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        )}

                        {/* Pagination */}
                        {buildings.last_page > 1 && (
                            <div className="mt-4 flex items-center justify-center gap-2">
                                {buildings.links.map((link, index) => (
                                    <Button
                                        key={index}
                                        variant={
                                            link.active ? "default" : "outline"
                                        }
                                        size="sm"
                                        disabled={!link.url}
                                        onClick={() =>
                                            link.url && router.get(link.url)
                                        }
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
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

BuildingsIndex.layout = {
    breadcrumbs: [
        { title: "Properties", href: buildingsIndex() },
        { title: "Buildings", href: buildingsIndex() },
    ],
};
