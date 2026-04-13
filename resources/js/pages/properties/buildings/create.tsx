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
import { index as buildingsIndex, store as buildingsStore } from '@/routes/buildings';

interface Community {
    id: number;
    name: string;
}

interface Props {
    communities: Community[];
    preselectedCommunityId?: number;
}

export default function BuildingCreate({ communities, preselectedCommunityId }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        community_id: preselectedCommunityId ? String(preselectedCommunityId) : '',
        no_floors: '',
        year_built: '',
        about: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(buildingsStore.url());
    };

    return (
        <>
            <Head title="Create Building" />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div>
                    <h1 className="text-2xl font-bold">Create Building</h1>
                    <p className="text-muted-foreground">Add a new building to your properties</p>
                </div>

                <Card className="max-w-2xl">
                    <CardHeader>
                        <CardTitle>Building Details</CardTitle>
                        <CardDescription>Enter the basic information for the new building</CardDescription>
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
                                    {processing ? 'Creating...' : 'Create Building'}
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

BuildingCreate.layout = {
    breadcrumbs: [
        { title: 'Properties', href: buildingsIndex() },
        { title: 'Buildings', href: buildingsIndex() },
        { title: 'Create', href: '#' },
    ],
};
