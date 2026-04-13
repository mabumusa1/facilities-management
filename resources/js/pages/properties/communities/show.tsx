import { Head, Link, router } from '@inertiajs/react';
import { Building2, Edit, MapPin, Plus, Trash2 } from 'lucide-react';
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
import { create as buildingsCreate, show as buildingsShow } from '@/routes/buildings';
import { index as communitiesIndex, edit as communitiesEdit, destroy as communitiesDestroy } from '@/routes/communities';

interface Building {
    id: number;
    name: string;
    status: string;
    no_floors: number;
    units_count: number;
}

interface Community {
    id: number;
    name: string;
    status: 'active' | 'inactive';
    description?: string;
    city?: { id: number; name: string };
    district?: { id: number; name: string };
    map?: { latitude: number; longitude: number };
    buildings: Building[];
    created_at: string;
    updated_at: string;
}

interface Props {
    community: Community;
}

export default function CommunityShow({ community }: Props) {
    const handleDelete = () => {
        router.delete(communitiesDestroy({ community: community.id }));
    };

    return (
        <>
            <Head title={community.name} />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">{community.name}</h1>
                        <div className="text-muted-foreground flex items-center gap-2">
                            <MapPin className="h-4 w-4" />
                            {community.city?.name ?? 'No city'}, {community.district?.name ?? 'No district'}
                        </div>
                    </div>
                    <div className="flex gap-2">
                        <Button variant="outline" asChild>
                            <Link href={communitiesEdit({ community: community.id })}>
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
                                    <DialogTitle>Delete Community</DialogTitle>
                                    <DialogDescription>
                                        Are you sure you want to delete this community? This action cannot be undone.
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
                <div className="grid gap-4 md:grid-cols-3">
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Status</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Badge variant={community.status === 'active' ? 'default' : 'secondary'}>
                                {community.status}
                            </Badge>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Buildings</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{community.buildings.length}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Total Units</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {community.buildings.reduce((acc, b) => acc + b.units_count, 0)}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Description */}
                {community.description && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Description</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-muted-foreground">{community.description}</p>
                        </CardContent>
                    </Card>
                )}

                {/* Buildings */}
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <div>
                            <CardTitle>Buildings</CardTitle>
                            <CardDescription>Buildings in this community</CardDescription>
                        </div>
                        <Button asChild>
                            <Link href={buildingsCreate() + `?community_id=${community.id}`}>
                                <Plus className="mr-2 h-4 w-4" />
                                Add Building
                            </Link>
                        </Button>
                    </CardHeader>
                    <CardContent>
                        {community.buildings.length === 0 ? (
                            <div className="text-muted-foreground py-8 text-center">
                                No buildings yet. Add your first building to this community.
                            </div>
                        ) : (
                            <div className="divide-y">
                                {community.buildings.map((building) => (
                                    <Link
                                        key={building.id}
                                        href={buildingsShow({ building: building.id })}
                                        className="hover:bg-muted/50 flex items-center justify-between p-4 transition-colors"
                                    >
                                        <div className="flex items-center gap-4">
                                            <div className="bg-primary/10 flex h-10 w-10 items-center justify-center rounded-lg">
                                                <Building2 className="text-primary h-5 w-5" />
                                            </div>
                                            <div>
                                                <div className="font-medium">{building.name}</div>
                                                <div className="text-muted-foreground text-sm">
                                                    {building.no_floors} floors
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4">
                                            <div className="text-muted-foreground text-sm">
                                                {building.units_count} units
                                            </div>
                                            <Badge variant={building.status === 'active' ? 'default' : 'secondary'}>
                                                {building.status}
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

CommunityShow.layout = {
    breadcrumbs: [
        { title: 'Properties', href: communitiesIndex() },
        { title: 'Communities', href: communitiesIndex() },
    ],
};
