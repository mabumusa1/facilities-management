import { Head, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { index as buildingsIndex, update as buildingsUpdate } from '@/routes/buildings';

interface Community {
    id: number;
    name: string;
}

interface Building {
    id: number;
    name: string;
    community_id?: number;
    no_floors: number;
    year_built?: number;
    about?: string;
    status: 'active' | 'inactive';
}

interface Props {
    building: Building;
    communities: Community[];
}

export default function BuildingEdit({ building, communities }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        name: building.name,
        community_id: building.community_id ? String(building.community_id) : '',
        no_floors: String(building.no_floors),
        year_built: building.year_built ? String(building.year_built) : '',
        about: building.about ?? '',
        status: building.status,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(buildingsUpdate({ building: building.id }));
    };

    return (
        <>
            <Head title={`Edit ${building.name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div>
                    <h1 className="text-2xl font-bold">Edit Building</h1>
                    <p className="text-muted-foreground">Update building details</p>
                </div>

                <Card className="max-w-2xl">
                    <CardHeader>
                        <CardTitle>Building Details</CardTitle>
                        <CardDescription>Modify the building information</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    placeholder="Enter building name"
                                />
                                {errors.name && (
                                    <p className="text-destructive text-sm">{errors.name}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="community_id">Community</Label>
                                <Select value={data.community_id} onValueChange={(v) => setData('community_id', v)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select community" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">No community</SelectItem>
                                        {communities.map((c) => (
                                            <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.community_id && (
                                    <p className="text-destructive text-sm">{errors.community_id}</p>
                                )}
                            </div>

                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="no_floors">Number of Floors</Label>
                                    <Input
                                        id="no_floors"
                                        type="number"
                                        min="1"
                                        value={data.no_floors}
                                        onChange={(e) => setData('no_floors', e.target.value)}
                                        placeholder="Enter number of floors"
                                    />
                                    {errors.no_floors && (
                                        <p className="text-destructive text-sm">{errors.no_floors}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="year_built">Year Built</Label>
                                    <Input
                                        id="year_built"
                                        type="number"
                                        min="1900"
                                        max={new Date().getFullYear()}
                                        value={data.year_built}
                                        onChange={(e) => setData('year_built', e.target.value)}
                                        placeholder="Enter year built"
                                    />
                                    {errors.year_built && (
                                        <p className="text-destructive text-sm">{errors.year_built}</p>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="status">Status</Label>
                                <Select value={data.status} onValueChange={(value: 'active' | 'inactive') => setData('status', value)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="active">Active</SelectItem>
                                        <SelectItem value="inactive">Inactive</SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.status && (
                                    <p className="text-destructive text-sm">{errors.status}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="about">About</Label>
                                <textarea
                                    id="about"
                                    value={data.about}
                                    onChange={(e) => setData('about', e.target.value)}
                                    placeholder="Enter building description (optional)"
                                    className="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-24 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                />
                                {errors.about && (
                                    <p className="text-destructive text-sm">{errors.about}</p>
                                )}
                            </div>

                            <div className="flex gap-4">
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Saving...' : 'Save Changes'}
                                </Button>
                                <Button type="button" variant="outline" onClick={() => window.history.back()}>
                                    Cancel
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

BuildingEdit.layout = {
    breadcrumbs: [
        { title: 'Properties', href: buildingsIndex() },
        { title: 'Buildings', href: buildingsIndex() },
        { title: 'Edit', href: '#' },
    ],
};
