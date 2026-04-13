import { Head, useForm } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    index as communitiesIndex,
    store as communitiesStore,
} from "@/routes/communities";

export default function CommunityCreate() {
    const { data, setData, post, processing, errors } = useForm({
        name: "",
        description: "",
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(communitiesStore.url());
    };

    return (
        <>
            <Head title="Create Community" />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div>
                    <h1 className="text-2xl font-bold">Create Community</h1>
                    <p className="text-muted-foreground">
                        Add a new community to your properties
                    </p>
                </div>

                <Card className="max-w-2xl">
                    <CardHeader>
                        <CardTitle>Community Details</CardTitle>
                        <CardDescription>
                            Enter the basic information for the new community
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={(e) =>
                                        setData("name", e.target.value)
                                    }
                                    placeholder="Enter community name"
                                />
                                {errors.name && (
                                    <p className="text-destructive text-sm">
                                        {errors.name}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="description">Description</Label>
                                <textarea
                                    id="description"
                                    value={data.description}
                                    onChange={(e) =>
                                        setData("description", e.target.value)
                                    }
                                    placeholder="Enter community description (optional)"
                                    className="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-24 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                />
                                {errors.description && (
                                    <p className="text-destructive text-sm">
                                        {errors.description}
                                    </p>
                                )}
                            </div>

                            <div className="flex gap-4">
                                <Button type="submit" disabled={processing}>
                                    {processing
                                        ? "Creating..."
                                        : "Create Community"}
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => window.history.back()}
                                >
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

CommunityCreate.layout = {
    breadcrumbs: [
        { title: "Properties", href: communitiesIndex() },
        { title: "Communities", href: communitiesIndex() },
        { title: "Create", href: "#" },
    ],
};
