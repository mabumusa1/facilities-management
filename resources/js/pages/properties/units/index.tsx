import { Head, Link, router } from "@inertiajs/react";
import { ArrowUpDown, Funnel, Search } from "lucide-react";
import { useState } from "react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { index as buildingsIndex } from "@/routes/buildings";
import { index as communitiesIndex } from "@/routes/communities";
import { create as contactsCreate } from "@/routes/contacts";
import {
    create as unitsCreate,
    index as unitsIndex,
    show as unitsShow,
} from "@/routes/units";

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

interface UnitType {
    id: number;
    name: string;
}

interface UnitStatus {
    id?: number;
    name?: string;
}

interface UnitTenant {
    id: number;
    name: string;
}

interface Unit {
    id: number;
    name: string;
    category?: UnitCategory;
    type?: UnitType;
    community?: Community;
    building?: Building;
    tenant?: UnitTenant | null;
    status?: UnitStatus | string | null;
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
        community_id?: string;
        building_id?: string;
        category_id?: string;
        sort?: string;
        direction?: string;
    };
    tabCounts?: {
        communities: number;
        buildings: number;
        units: number;
    };
}

export default function UnitsIndex({
    units,
    communities,
    buildings,
    categories,
    filters,
    tabCounts,
}: Props) {
    const [search, setSearch] = useState(filters.search ?? "");
    const [communityId, setCommunityId] = useState(filters.community_id ?? "");
    const [buildingId, setBuildingId] = useState(filters.building_id ?? "");
    const [categoryId, setCategoryId] = useState(filters.category_id ?? "");
    const [sortValue, setSortValue] = useState(
        `${filters.sort ?? "created_at"}_${filters.direction ?? "desc"}`,
    );

    const buildFilters = (
        next?: Partial<{
            communityId: string;
            buildingId: string;
            categoryId: string;
            sortValue: string;
        }>,
    ) => {
        const currentCommunityId = next?.communityId ?? communityId;
        const currentBuildingId = next?.buildingId ?? buildingId;
        const currentCategoryId = next?.categoryId ?? categoryId;
        const currentSort = next?.sortValue ?? sortValue;
        const [sort = "created_at", direction = "desc"] = currentSort.split("_");

        return {
            search,
            community_id: currentCommunityId,
            building_id: currentBuildingId,
            category_id: currentCategoryId,
            sort,
            direction,
        };
    };

    const handleSearch = () => {
        router.get(unitsIndex(), buildFilters(), { preserveState: true });
    };

    const handleSortChange = (value: string) => {
        setSortValue(value);

        router.get(unitsIndex(), buildFilters({ sortValue: value }), {
            preserveState: true,
        });
    };

    const getStatusLabel = (status?: UnitStatus | string | null): string => {
        if (typeof status === "string") {
            return status;
        }

        return status?.name ?? "N/A";
    };

    return (
        <>
            <Head title="Units" />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Units</h1>
                        <p className="text-muted-foreground">Properties list</p>
                    </div>
                </div>

                <div className="flex flex-wrap gap-2">
                    <Button asChild variant="outline">
                        <Link href={communitiesIndex()}>
                            Communities ({tabCounts?.communities ?? 0})
                        </Link>
                    </Button>
                    <Button asChild variant="outline">
                        <Link href={buildingsIndex()}>
                            Buildings ({tabCounts?.buildings ?? 0})
                        </Link>
                    </Button>
                    <Button asChild>
                        <Link href={unitsIndex()}>
                            Units ({tabCounts?.units ?? units.total})
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardContent className="space-y-4 pt-6">
                        <div className="flex flex-wrap items-center justify-between gap-4">
                            <div className="flex flex-wrap items-center gap-3">
                                <div className="relative w-full min-w-64 md:w-96">
                                    <Search className="text-muted-foreground absolute top-2.5 left-2.5 h-4 w-4" />
                                    <Input
                                        placeholder="Search"
                                        value={search}
                                        onChange={(event) => setSearch(event.target.value)}
                                        onKeyDown={(event) =>
                                            event.key === "Enter" && handleSearch()
                                        }
                                        className="pl-8"
                                    />
                                </div>

                                <Select value={sortValue} onValueChange={handleSortChange}>
                                    <SelectTrigger className="w-44">
                                        <div className="flex items-center gap-2">
                                            <ArrowUpDown className="h-4 w-4" />
                                            <SelectValue placeholder="Sort" />
                                        </div>
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="created_at_desc">Newest</SelectItem>
                                        <SelectItem value="created_at_asc">Oldest</SelectItem>
                                        <SelectItem value="name_asc">Name A-Z</SelectItem>
                                        <SelectItem value="name_desc">Name Z-A</SelectItem>
                                    </SelectContent>
                                </Select>

                                <Button variant="outline" type="button">
                                    <Funnel className="mr-2 h-4 w-4" />
                                    Filter by
                                </Button>

                                <Select
                                    value={communityId}
                                    onValueChange={(value) => {
                                        setCommunityId(value);
                                        router.get(
                                            unitsIndex(),
                                            buildFilters({ communityId: value }),
                                            { preserveState: true },
                                        );
                                    }}
                                >
                                    <SelectTrigger className="w-48">
                                        <SelectValue placeholder="Community" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All communities</SelectItem>
                                        {communities.map((community) => (
                                            <SelectItem
                                                key={community.id}
                                                value={String(community.id)}
                                            >
                                                {community.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>

                                <Select
                                    value={buildingId}
                                    onValueChange={(value) => {
                                        setBuildingId(value);
                                        router.get(
                                            unitsIndex(),
                                            buildFilters({ buildingId: value }),
                                            { preserveState: true },
                                        );
                                    }}
                                >
                                    <SelectTrigger className="w-48">
                                        <SelectValue placeholder="Building" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All buildings</SelectItem>
                                        {buildings.map((building) => (
                                            <SelectItem
                                                key={building.id}
                                                value={String(building.id)}
                                            >
                                                {building.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>

                                <Select
                                    value={categoryId}
                                    onValueChange={(value) => {
                                        setCategoryId(value);
                                        router.get(
                                            unitsIndex(),
                                            buildFilters({ categoryId: value }),
                                            { preserveState: true },
                                        );
                                    }}
                                >
                                    <SelectTrigger className="w-48">
                                        <SelectValue placeholder="Category" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All categories</SelectItem>
                                        {categories.map((category) => (
                                            <SelectItem
                                                key={category.id}
                                                value={String(category.id)}
                                            >
                                                {category.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>

                                <Button variant="outline" onClick={handleSearch}>
                                    Search
                                </Button>
                            </div>

                            <Button asChild>
                                <Link href={unitsCreate()}>Add Unit</Link>
                            </Button>
                        </div>

                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Unit Name</TableHead>
                                    <TableHead>Type</TableHead>
                                    <TableHead>Community</TableHead>
                                    <TableHead>Building</TableHead>
                                    <TableHead>Owner</TableHead>
                                    <TableHead>Tenant</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead className="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {units.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={8}
                                            className="text-muted-foreground py-8 text-center"
                                        >
                                            No units found.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    units.data.map((unit) => (
                                        <TableRow key={unit.id}>
                                            <TableCell className="font-medium">
                                                {unit.name}
                                            </TableCell>
                                            <TableCell>
                                                <div className="space-y-0.5">
                                                    <p>{unit.type?.name ?? "-"}</p>
                                                    <p className="text-muted-foreground text-xs">
                                                        {unit.category?.name ?? "-"}
                                                    </p>
                                                </div>
                                            </TableCell>
                                            <TableCell>{unit.community?.name ?? "-"}</TableCell>
                                            <TableCell>{unit.building?.name ?? "-"}</TableCell>
                                            <TableCell>
                                                <Button asChild variant="outline" size="sm">
                                                    <Link
                                                        href={contactsCreate({
                                                            query: {
                                                                type: "owner",
                                                                unit_id: unit.id,
                                                            },
                                                        })}
                                                    >
                                                        Add
                                                    </Link>
                                                </Button>
                                            </TableCell>
                                            <TableCell>
                                                {unit.tenant?.name ?? "N/A"}
                                            </TableCell>
                                            <TableCell>
                                                <Badge variant="secondary">
                                                    {getStatusLabel(unit.status)}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Button asChild size="sm" variant="ghost">
                                                    <Link
                                                        href={unitsShow({ unit: unit.id })}
                                                    >
                                                        Details
                                                    </Link>
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>

                        {units.last_page > 1 && (
                            <div className="mt-4 flex items-center justify-center gap-2">
                                {units.links.map((link, index) => (
                                    <Button
                                        key={index}
                                        variant={link.active ? "default" : "outline"}
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
        { title: "Properties", href: unitsIndex() },
        { title: "Units", href: unitsIndex() },
    ],
};
