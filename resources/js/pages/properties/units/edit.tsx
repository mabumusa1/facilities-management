import { Head, useForm } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { index as unitsIndex, update as unitsUpdate } from "@/routes/units";

interface Community {
    id: number;
    name: string;
}

interface Building {
    id: number;
    name: string;
    community_id?: number;
}

interface UnitCategory {
    id: number;
    name: string;
}

interface UnitType {
    id: number;
    name: string;
}

interface Unit {
    id: number;
    name: string;
    community_id: number;
    building_id?: number;
    unit_category_id: number;
    unit_type_id: number;
    floor_no?: number;
    net_area?: number;
    year_built?: number;
    market_rent?: number;
    about?: string;
    status: "active" | "inactive";
    is_marketplace: boolean;
    is_off_plan_sale: boolean;
}

interface Props {
    unit: Unit;
    communities: Community[];
    buildings: Building[];
    categories: UnitCategory[];
    types: UnitType[];
}

export default function UnitEdit({
    unit,
    communities,
    buildings,
    categories,
    types,
}: Props) {
    const { data, setData, put, processing, errors } = useForm({
        name: unit.name,
        community_id: String(unit.community_id),
        building_id: unit.building_id ? String(unit.building_id) : "",
        unit_category_id: String(unit.unit_category_id),
        unit_type_id: String(unit.unit_type_id),
        floor_no: unit.floor_no !== undefined ? String(unit.floor_no) : "",
        net_area: unit.net_area !== undefined ? String(unit.net_area) : "",
        year_built:
            unit.year_built !== undefined ? String(unit.year_built) : "",
        market_rent:
            unit.market_rent !== undefined ? String(unit.market_rent) : "",
        about: unit.about ?? "",
        status: unit.status,
        is_marketplace: unit.is_marketplace,
        is_off_plan_sale: unit.is_off_plan_sale,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(unitsUpdate.url({ unit: unit.id }));
    };

    // Filter buildings by selected community
    const filteredBuildings = data.community_id
        ? buildings.filter((b) => b.community_id === Number(data.community_id))
        : buildings;

    return (
        <>
            <Head title={`Edit ${unit.name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div>
                    <h1 className="text-2xl font-bold">Edit Unit</h1>
                    <p className="text-muted-foreground">Update unit details</p>
                </div>

                <Card className="max-w-2xl">
                    <CardHeader>
                        <CardTitle>Unit Details</CardTitle>
                        <CardDescription>
                            Modify the unit information
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
                                    placeholder="Enter unit name"
                                />
                                {errors.name && (
                                    <p className="text-destructive text-sm">
                                        {errors.name}
                                    </p>
                                )}
                            </div>

                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="community_id">
                                        Community
                                    </Label>
                                    <Select
                                        value={data.community_id}
                                        onValueChange={(v) =>
                                            setData("community_id", v)
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select community" />
                                        </SelectTrigger>
                                        <SelectContent>
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
                                    {errors.community_id && (
                                        <p className="text-destructive text-sm">
                                            {errors.community_id}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="building_id">
                                        Building (Optional)
                                    </Label>
                                    <Select
                                        value={data.building_id}
                                        onValueChange={(v) =>
                                            setData("building_id", v)
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select building" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="">
                                                No building
                                            </SelectItem>
                                            {filteredBuildings.map((b) => (
                                                <SelectItem
                                                    key={b.id}
                                                    value={String(b.id)}
                                                >
                                                    {b.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.building_id && (
                                        <p className="text-destructive text-sm">
                                            {errors.building_id}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="unit_category_id">
                                        Category
                                    </Label>
                                    <Select
                                        value={data.unit_category_id}
                                        onValueChange={(v) =>
                                            setData("unit_category_id", v)
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select category" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {categories.map((cat) => (
                                                <SelectItem
                                                    key={cat.id}
                                                    value={String(cat.id)}
                                                >
                                                    {cat.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.unit_category_id && (
                                        <p className="text-destructive text-sm">
                                            {errors.unit_category_id}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="unit_type_id">Type</Label>
                                    <Select
                                        value={data.unit_type_id}
                                        onValueChange={(v) =>
                                            setData("unit_type_id", v)
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select type" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {types.map((type) => (
                                                <SelectItem
                                                    key={type.id}
                                                    value={String(type.id)}
                                                >
                                                    {type.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.unit_type_id && (
                                        <p className="text-destructive text-sm">
                                            {errors.unit_type_id}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="grid gap-4 md:grid-cols-3">
                                <div className="space-y-2">
                                    <Label htmlFor="floor_no">
                                        Floor Number
                                    </Label>
                                    <Input
                                        id="floor_no"
                                        type="number"
                                        min="0"
                                        value={data.floor_no}
                                        onChange={(e) =>
                                            setData("floor_no", e.target.value)
                                        }
                                        placeholder="e.g., 1"
                                    />
                                    {errors.floor_no && (
                                        <p className="text-destructive text-sm">
                                            {errors.floor_no}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="net_area">
                                        Net Area (sqm)
                                    </Label>
                                    <Input
                                        id="net_area"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        value={data.net_area}
                                        onChange={(e) =>
                                            setData("net_area", e.target.value)
                                        }
                                        placeholder="e.g., 75.5"
                                    />
                                    {errors.net_area && (
                                        <p className="text-destructive text-sm">
                                            {errors.net_area}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="year_built">
                                        Year Built
                                    </Label>
                                    <Input
                                        id="year_built"
                                        type="number"
                                        min="1900"
                                        max={new Date().getFullYear()}
                                        value={data.year_built}
                                        onChange={(e) =>
                                            setData(
                                                "year_built",
                                                e.target.value,
                                            )
                                        }
                                        placeholder="e.g., 2020"
                                    />
                                    {errors.year_built && (
                                        <p className="text-destructive text-sm">
                                            {errors.year_built}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="market_rent">
                                        Market Rent
                                    </Label>
                                    <Input
                                        id="market_rent"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        value={data.market_rent}
                                        onChange={(e) =>
                                            setData(
                                                "market_rent",
                                                e.target.value,
                                            )
                                        }
                                        placeholder="e.g., 1500.00"
                                    />
                                    {errors.market_rent && (
                                        <p className="text-destructive text-sm">
                                            {errors.market_rent}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="status">Status</Label>
                                    <Select
                                        value={data.status}
                                        onValueChange={(
                                            value: "active" | "inactive",
                                        ) => setData("status", value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="active">
                                                Active
                                            </SelectItem>
                                            <SelectItem value="inactive">
                                                Inactive
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {errors.status && (
                                        <p className="text-destructive text-sm">
                                            {errors.status}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="about">About</Label>
                                <textarea
                                    id="about"
                                    value={data.about}
                                    onChange={(e) =>
                                        setData("about", e.target.value)
                                    }
                                    placeholder="Enter unit description (optional)"
                                    className="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-24 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                />
                                {errors.about && (
                                    <p className="text-destructive text-sm">
                                        {errors.about}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-4">
                                <div className="flex items-center space-x-2">
                                    <Checkbox
                                        id="is_marketplace"
                                        checked={data.is_marketplace}
                                        onCheckedChange={(checked) =>
                                            setData(
                                                "is_marketplace",
                                                checked === true,
                                            )
                                        }
                                    />
                                    <Label
                                        htmlFor="is_marketplace"
                                        className="cursor-pointer"
                                    >
                                        List on Marketplace
                                    </Label>
                                </div>

                                <div className="flex items-center space-x-2">
                                    <Checkbox
                                        id="is_off_plan_sale"
                                        checked={data.is_off_plan_sale}
                                        onCheckedChange={(checked) =>
                                            setData(
                                                "is_off_plan_sale",
                                                checked === true,
                                            )
                                        }
                                    />
                                    <Label
                                        htmlFor="is_off_plan_sale"
                                        className="cursor-pointer"
                                    >
                                        Off-Plan Sale
                                    </Label>
                                </div>
                            </div>

                            <div className="flex gap-4">
                                <Button type="submit" disabled={processing}>
                                    {processing ? "Saving..." : "Save Changes"}
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

UnitEdit.layout = {
    breadcrumbs: [
        { title: "Properties", href: unitsIndex() },
        { title: "Units", href: unitsIndex() },
        { title: "Edit", href: "#" },
    ],
};
