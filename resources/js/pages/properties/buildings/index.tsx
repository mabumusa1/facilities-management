import { Head, Link, router } from "@inertiajs/react";
import { ArrowUpDown, Search } from "lucide-react";
import { useState } from "react";
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
import {
    create as buildingsCreate,
    index as buildingsIndex,
    show as buildingsShow,
} from "@/routes/buildings";
import { index as communitiesIndex } from "@/routes/communities";
import { index as unitsIndex } from "@/routes/units";

interface Community {
    id: number;
    name: string;
}

interface Location {
    id: number;
    name: string;
}

interface Building {
    id: number;
    name: string;
    community?: Community;
    city?: Location;
    district?: Location;
    units_count: number | string;
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
        community_id?: string;
        sort?: string;
        direction?: string;
    };
    tabCounts?: {
        communities: number;
        buildings: number;
        units: number;
    };
}

export default function BuildingsIndex({
    buildings,
    communities,
    filters,
    tabCounts,
}: Props) {
    const [search, setSearch] = useState(filters.search ?? "");
    const [communityId, setCommunityId] = useState(filters.community_id ?? "");
    const [sortValue, setSortValue] = useState(
        `${filters.sort ?? "created_at"}_${filters.direction ?? "desc"}`,
    );

    const applyFilters = (next?: Partial<{ communityId: string; sortValue: string }>) => {
        const currentCommunityId = next?.communityId ?? communityId;
        const currentSortValue = next?.sortValue ?? sortValue;
        const [sort = "created_at", direction = "desc"] = currentSortValue.split("_");

        return {
            search,
            community_id: currentCommunityId,
            sort,
            direction,
        };
    };

    const handleSearch = () => {
        router.get(buildingsIndex(), applyFilters(), { preserveState: true });
    };

    const handleSortChange = (value: string) => {
        setSortValue(value);

        router.get(buildingsIndex(), applyFilters({ sortValue: value }), {
            preserveState: true,
        });
    };

    const handleCommunityChange = (value: string) => {
        setCommunityId(value);

        router.get(buildingsIndex(), applyFilters({ communityId: value }), {
            preserveState: true,
        });
    };

    return (
        <>
            <Head title="Buildings" />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Buildings</h1>
                        <p className="text-muted-foreground">Properties list</p>
                    </div>
                </div>

                <div className="flex flex-wrap gap-2">
                    <Button asChild variant="outline">
                        <Link href={communitiesIndex()}>
                            Communities ({tabCounts?.communities ?? 0})
                        </Link>
                    </Button>
                    <Button asChild>
                        <Link href={buildingsIndex()}>
                            Buildings ({tabCounts?.buildings ?? buildings.total})
                        </Link>
                    </Button>
                    <Button asChild variant="outline">
                        <Link href={unitsIndex()}>Units ({tabCounts?.units ?? 0})</Link>
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

                                <Select value={communityId} onValueChange={handleCommunityChange}>
                                    <SelectTrigger className="w-52">
                                        <SelectValue placeholder="All communities" />
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

                                <Button variant="outline" onClick={handleSearch}>
                                    Search
                                </Button>
                            </div>

                            <Button asChild>
                                <Link href={buildingsCreate()}>Add Building</Link>
                            </Button>
                        </div>

                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Building Name</TableHead>
                                    <TableHead>Community Name</TableHead>
                                    <TableHead>City</TableHead>
                                    <TableHead>District</TableHead>
                                    <TableHead>Units</TableHead>
                                    <TableHead className="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {buildings.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={6}
                                            className="text-muted-foreground py-8 text-center"
                                        >
                                            No buildings found.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    buildings.data.map((building) => (
                                        <TableRow key={building.id}>
                                            <TableCell className="font-medium">
                                                {building.name}
                                            </TableCell>
                                            <TableCell>
                                                {building.community?.name ?? "-"}
                                            </TableCell>
                                            <TableCell>{building.city?.name ?? "-"}</TableCell>
                                            <TableCell>
                                                {building.district?.name ?? "-"}
                                            </TableCell>
                                            <TableCell>
                                                {Number(building.units_count ?? 0)}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="flex justify-end gap-2">
                                                    <Button asChild size="sm" variant="outline">
                                                        <Link
                                                            href={unitsIndex({
                                                                query: {
                                                                    building_id: building.id,
                                                                },
                                                            })}
                                                        >
                                                            Properties
                                                        </Link>
                                                    </Button>
                                                    <Button asChild size="sm" variant="ghost">
                                                        <Link
                                                            href={buildingsShow({
                                                                building: building.id,
                                                            })}
                                                        >
                                                            Details
                                                        </Link>
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>

                        {buildings.last_page > 1 && (
                            <div className="mt-4 flex items-center justify-center gap-2">
                                {buildings.links.map((link, index) => (
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

BuildingsIndex.layout = {
    breadcrumbs: [
        { title: "Properties", href: buildingsIndex() },
        { title: "Buildings", href: buildingsIndex() },
    ],
};
