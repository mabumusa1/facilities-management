import { Head, Link, router } from '@inertiajs/react';
import { Building2, Edit, Home, Plus, Trash2 } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { index as buildingsIndex, edit as buildingsEdit, destroy as buildingsDestroy } from '@/routes/buildings';
import { show as communitiesShow } from '@/routes/communities';
import { create as unitsCreate, show as unitsShow } from '@/routes/units';

interface Unit {
    id: number;
    name: string;
    status: string;
    floor_no?: number;
    net_area?: number;
    market_rent?: number;
}

interface Community {
    id: number;
    name: string;
}

interface Building {
    id: number;
    name: string;
    status: 'active' | 'inactive';
    no_floors: number;
    year_built?: number;
    about?: string;
    community?: Community;
    units: Unit[];
    created_at: string;
    updated_at: string;
}

interface Props {
    building: Building;
}

export default function BuildingShow({ building }: Props) {
    const handleDelete = () => {
        router.delete(buildingsDestroy({ building: building.id }));
    };

    return (
        <>
            <Head title={building.name} />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">{building.name}</h1>
                        <div className="text-muted-foreground flex items-center gap-2">
                            <Building2 className="h-4 w-4" />
                            {building.community ? (
                                <Link
                                    href={communitiesShow({ community: building.community.id })}
                                    className="hover:underline"
                                >
                                    {building.community.name}
                                </Link>
                            ) : (
                                'No community'
                            )}
                        </div>
                    </div>
                    <div className="flex gap-2">
                        <Button variant="outline" asChild>
                            <Link href={buildingsEdit({ building: building.id })}>
                                <Edit className="mr-2 h-4 w-4" />
                                Edit
                            </Link>
                        </Button>
                        <Dialog>
                            <DialogTrigger asChild>
                                <Button variant="destructive">
                                    <Trash2 className="mr-2 h-4 w-4" />
                                    Delete
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Delete Building</DialogTitle>
                                    <DialogDescription>
                                        Are you sure you want to delete this building? This action cannot be undone.
                                    </DialogDescription>
                                </DialogHeader>
                                <DialogFooter>
                                    <Button variant="outline">Cancel</Button>
                                    <Button variant="destructive" onClick={handleDelete}>Delete</Button>
                                </DialogFooter>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>

                {/* Overview Cards */}
                <div className="grid gap-4 md:grid-cols-4">
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Status</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Badge variant={building.status === 'active' ? 'default' : 'secondary'}>
                                {building.status}
                            </Badge>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Floors</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{building.no_floors}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Units</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{building.units.length}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Year Built</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{building.year_built ?? 'N/A'}</div>
                        </CardContent>
                    </Card>
                </div>

                {/* Description */}
                {building.about && (
                    <Card>
                        <CardHeader>
                            <CardTitle>About</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-muted-foreground">{building.about}</p>
                        </CardContent>
                    </Card>
                )}

                {/* Units */}
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <div>
                            <CardTitle>Units</CardTitle>
                            <CardDescription>Units in this building</CardDescription>
                        </div>
                        <Button asChild>
                            <Link href={unitsCreate() + `?building_id=${building.id}`}>
                                <Plus className="mr-2 h-4 w-4" />
                                Add Unit
                            </Link>
                        </Button>
                    </CardHeader>
                    <CardContent>
                        {building.units.length === 0 ? (
                            <div className="text-muted-foreground py-8 text-center">
                                No units yet. Add your first unit to this building.
                            </div>
                        ) : (
                            <div className="divide-y">
                                {building.units.map((unit) => (
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
                                                    {unit.floor_no !== undefined ? `Floor ${unit.floor_no}` : 'No floor'}
                                                    {unit.net_area && ` • ${unit.net_area} sqm`}
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4">
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
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

BuildingShow.layout = {
    breadcrumbs: [
        { title: 'Properties', href: buildingsIndex() },
        { title: 'Buildings', href: buildingsIndex() },
    ],
};
