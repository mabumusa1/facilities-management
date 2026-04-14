import { Head, Link, router } from "@inertiajs/react";
import { ArrowUpDown, Search } from "lucide-react";
import { useState } from "react";
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
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { index as buildingsIndex } from "@/routes/buildings";
import {
    index as communitiesIndex,
} from "@/routes/communities";
import { index as unitsIndex } from "@/routes/units";

interface Community {
    id: number;
    name: string | null;
    status: "active" | "inactive";
    city?: { id: number; name: string };
    district?: { id: number; name: string };
    buildings_count: number | string;
    units_count: number | string;
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
        sortBy?: string;
        sortDirection?: "asc" | "desc";
    };
    tabCounts: {
        communities: number;
        buildings: number;
        units: number;
    };
}

export default function CommunitiesIndex({
    communities,
    filters,
    tabCounts,
}: Props) {
    const [search, setSearch] = useState(filters.search ?? "");
    const [sortValue, setSortValue] = useState(() => {
        const sortBy = filters.sortBy ?? "created_at";
        const sortDirection = filters.sortDirection ?? "desc";

        return `${sortBy}_${sortDirection}`;
    });

    const buildFilters = (next?: Partial<{ search: string; sortValue: string }>) => {
        const activeSearch = next?.search ?? search;
        const activeSort = next?.sortValue ?? sortValue;
        const [sortBy = "created_at", sortDirection = "desc"] = activeSort.split("_");

        return {
            search: activeSearch,
            sortBy,
            sortDirection,
        };
    };

    const handleSearch = () => {
        router.get(communitiesIndex(), buildFilters(), { preserveState: true });
    };

    const handleSortChange = (value: string) => {
        setSortValue(value);
        router.get(
            communitiesIndex(),
            buildFilters({ sortValue: value }),
            { preserveState: true },
        );
    };

    const formatCount = (value: number | string) => Number(value ?? 0);

    return (
        <>
            <Head title="Communities" />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Communities</h1>
                        <p className="text-muted-foreground">
                            Properties list
                        </p>
                    </div>
                </div>

                <div className="flex flex-wrap gap-2">
                    <Button asChild variant="default">
                        <Link href={communitiesIndex()}>
                            Communities ({tabCounts.communities})
                        </Link>
                    </Button>
                    <Button asChild variant="outline">
                        <Link href={buildingsIndex()}>
                            Buildings ({tabCounts.buildings})
                        </Link>
                    </Button>
                    <Button asChild variant="outline">
                        <Link href={unitsIndex()}>Units ({tabCounts.units})</Link>
                    </Button>
                </div>

                <Card>
                    <CardContent className="pt-6">
                        <div className="flex flex-wrap items-center justify-between gap-4">
                            <Button asChild>
                                <Link href="/properties-list/new/community">
                                    Add Community
                                </Link>
                            </Button>

                            <div className="flex flex-wrap items-center gap-3">
                                <Select
                                    value={sortValue}
                                    onValueChange={handleSortChange}
                                >
                                    <SelectTrigger className="w-44">
                                        <div className="flex items-center gap-2">
                                            <ArrowUpDown className="h-4 w-4" />
                                            <SelectValue placeholder="Sort" />
                                        </div>
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="created_at_desc">
                                            Newest
                                        </SelectItem>
                                        <SelectItem value="created_at_asc">
                                            Oldest
                                        </SelectItem>
                                        <SelectItem value="name_asc">
                                            Name A-Z
                                        </SelectItem>
                                        <SelectItem value="name_desc">
                                            Name Z-A
                                        </SelectItem>
                                    </SelectContent>
                                </Select>

                                <Button variant="outline" onClick={handleSearch}>
                                    Search
                                </Button>
                            </div>
                        </div>

                        <div className="mt-4 flex gap-4">
                            <div className="relative flex-1">
                                <Search className="text-muted-foreground absolute top-2.5 left-2.5 h-4 w-4" />
                                <Input
                                    placeholder="Search"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    onKeyDown={(e) =>
                                        e.key === "Enter" && handleSearch()
                                    }
                                    className="pl-8"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>
                            {communities.total}{" "}
                            {communities.total === 1
                                ? "Community"
                                : "Communities"}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Community Name</TableHead>
                                    <TableHead>City</TableHead>
                                    <TableHead>District</TableHead>
                                    <TableHead>Buildings</TableHead>
                                    <TableHead>Units</TableHead>
                                    <TableHead className="text-right">
                                        Actions
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {communities.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={6}
                                            className="text-muted-foreground py-8 text-center"
                                        >
                                            No communities found.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    communities.data.map((community) => (
                                        <TableRow key={community.id}>
                                            <TableCell className="font-medium">
                                                {community.name ?? "-"}
                                            </TableCell>
                                            <TableCell>
                                                {community.city?.name ?? "-"}
                                            </TableCell>
                                            <TableCell>
                                                {community.district?.name ??
                                                    "-"}
                                            </TableCell>
                                            <TableCell>
                                                {formatCount(
                                                    community.buildings_count,
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                {formatCount(
                                                    community.units_count,
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="flex justify-end gap-2">
                                                    <Button
                                                        asChild
                                                        size="sm"
                                                        variant="outline"
                                                    >
                                                        <Link
                                                            href={
                                                                buildingsIndex() +
                                                                `?community_id=${community.id}`
                                                            }
                                                        >
                                                            Properties
                                                        </Link>
                                                    </Button>
                                                    <Button
                                                        asChild
                                                        size="sm"
                                                        variant="ghost"
                                                    >
                                                        <Link
                                                            href={`/properties-list/communities/community/details/${community.id}`}
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

                        {communities.last_page > 1 && (
                            <div className="mt-4 flex items-center justify-center gap-2">
                                {communities.links.map((link, index) => (
                                    <Button
                                        key={index}
                                        variant={
                                            link.active ? "default" : "outline"
                                        }
                                        size="sm"
                                        disabled={!link.url}
                                        onClick={() => link.url && router.get(link.url)}
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

CommunitiesIndex.layout = {
    breadcrumbs: [
        { title: "Properties", href: communitiesIndex() },
        { title: "Communities", href: communitiesIndex() },
    ],
};
