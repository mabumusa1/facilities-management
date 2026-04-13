import { Head, Link, router } from '@inertiajs/react';
import { Building2, Edit, Home, MapPin, Trash2 } from 'lucide-react';
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
import { index as unitsIndex, edit as unitsEdit, destroy as unitsDestroy } from '@/routes/units';
import { show as buildingsShow } from '@/routes/buildings';
import { show as communitiesShow } from '@/routes/communities';

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

interface Status {
    id: number;
    name: string;
    color?: string;
}

interface Unit {
    id: number;
    name: string;
    status: 'active' | 'inactive';
    floor_no?: number;
    net_area?: number;
    year_built?: number;
    market_rent?: number;
    about?: string;
    is_marketplace: boolean;
    is_off_plan_sale: boolean;
    photos?: string[];
    map?: { latitude: number; longitude: number };
    community?: Community;
    building?: Building;
    category?: UnitCategory;
    type?: UnitType;
    statusRelation?: Status;
    created_at: string;
    updated_at: string;
}

interface Props {
    unit: Unit;
}

export default function UnitShow({ unit }: Props) {
    const handleDelete = () => {
        router.delete(unitsDestroy({ unit: unit.id }));
    };

    return (
        <>
            <Head title={unit.name} />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">{unit.name}</h1>
                        <div className="text-muted-foreground flex items-center gap-2">
                            {unit.building ? (
                                <>
                                    <Building2 className="h-4 w-4" />
                                    <Link
                                        href={buildingsShow({ building: unit.building.id })}
                                        className="hover:underline"
                                    >
                                        {unit.building.name}
                                    </Link>
                                </>
                            ) : unit.community ? (
                                <>
                                    <MapPin className="h-4 w-4" />
                                    <Link
                                        href={communitiesShow({ community: unit.community.id })}
                                        className="hover:underline"
                                    >
                                        {unit.community.name}
                                    </Link>
                                </>
                            ) : (
                                <>
                                    <Home className="h-4 w-4" />
                                    <span>No location assigned</span>
                                </>
                            )}
                        </div>
                    </div>
                    <div className="flex gap-2">
                        <Button variant="outline" asChild>
                            <Link href={unitsEdit({ unit: unit.id })}>
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
                                    <DialogTitle>Delete Unit</DialogTitle>
                                    <DialogDescription>
                                        Are you sure you want to delete this unit? This action cannot be undone.
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
                            <Badge variant={unit.status === 'active' ? 'default' : 'secondary'}>
                                {unit.status}
                            </Badge>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Category</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-lg font-medium">{unit.category?.name ?? 'N/A'}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Type</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-lg font-medium">{unit.type?.name ?? 'N/A'}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Market Rent</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {unit.market_rent ? `$${unit.market_rent.toLocaleString()}` : 'N/A'}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Details Grid */}
                <div className="grid gap-4 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Unit Details</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Floor</span>
                                <span className="font-medium">{unit.floor_no ?? 'N/A'}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Net Area</span>
                                <span className="font-medium">{unit.net_area ? `${unit.net_area} sqm` : 'N/A'}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Year Built</span>
                                <span className="font-medium">{unit.year_built ?? 'N/A'}</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Marketplace</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Listed on Marketplace</span>
                                <Badge variant={unit.is_marketplace ? 'default' : 'secondary'}>
                                    {unit.is_marketplace ? 'Yes' : 'No'}
                                </Badge>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Off-Plan Sale</span>
                                <Badge variant={unit.is_off_plan_sale ? 'default' : 'secondary'}>
                                    {unit.is_off_plan_sale ? 'Yes' : 'No'}
                                </Badge>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Description */}
                {unit.about && (
                    <Card>
                        <CardHeader>
                            <CardTitle>About</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-muted-foreground">{unit.about}</p>
                        </CardContent>
                    </Card>
                )}

                {/* Photos */}
                {unit.photos && unit.photos.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Photos</CardTitle>
                            <CardDescription>{unit.photos.length} photo(s)</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="grid gap-4 md:grid-cols-4">
                                {unit.photos.map((photo, index) => (
                                    <div key={index} className="bg-muted aspect-square rounded-lg">
                                        <img
                                            src={photo}
                                            alt={`Unit photo ${index + 1}`}
                                            className="h-full w-full rounded-lg object-cover"
                                        />
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Map */}
                {unit.map && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Location</CardTitle>
                            <CardDescription>
                                Coordinates: {unit.map.latitude}, {unit.map.longitude}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="bg-muted flex h-64 items-center justify-center rounded-lg">
                                <span className="text-muted-foreground">Map placeholder</span>
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </>
    );
}

UnitShow.layout = {
    breadcrumbs: [
        { title: 'Properties', href: unitsIndex() },
        { title: 'Units', href: unitsIndex() },
    ],
};
