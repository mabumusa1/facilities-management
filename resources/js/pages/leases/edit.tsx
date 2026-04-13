import { Head, Link, useForm } from "@inertiajs/react";
import {
    FileText,
    ArrowLeft,
    Save,
    Calendar,
    DollarSign,
    Building2,
    User,
    Home,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
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
import { Textarea } from "@/components/ui/textarea";

interface Unit {
    id: number;
    name: string;
    building_id: number;
    pivot?: {
        annual_rental_amount: string;
        net_area: string;
        meter_cost: string;
    };
}

interface Contact {
    id: number;
    name: string;
    email?: string;
    phone?: string;
}

interface Status {
    id: number;
    name: string;
}

interface Community {
    id: number;
    name: string;
}

interface Building {
    id: number;
    name: string;
    community_id: number;
}

interface Lease {
    id: number;
    contract_number: string | null;
    tenant_id: number | null;
    community_id: number | null;
    building_id: number | null;
    status_id: number | null;
    tenant: Contact | null;
    units: Unit[];
    status: Status | null;
    start_date: string;
    end_date: string;
    handover_date: string | null;
    rental_total_amount: string;
    security_deposit_amount: string | null;
    security_deposit_due_date: string | null;
    tenant_type: string;
    rental_type: string;
    number_of_years: number;
    number_of_months: number;
    number_of_days: number;
    free_period: number | null;
    terms_conditions: string | null;
    is_terms: boolean;
}

interface LeaseEditProps {
    lease: Lease;
    communities: Community[];
    buildings: Building[];
    units: Unit[];
    tenants: Contact[];
    statuses: Status[];
}

export default function LeaseEdit({
    lease,
    communities,
    buildings,
    units,
    tenants,
    statuses,
}: LeaseEditProps) {
    const { data, setData, put, processing, errors } = useForm({
        contract_number: lease.contract_number || "",
        tenant_id: lease.tenant_id || "",
        community_id: lease.community_id || "",
        building_id: lease.building_id || "",
        status_id: lease.status_id || "",
        units: lease.units.map((u) => ({
            id: u.id,
            annual_rental_amount: u.pivot?.annual_rental_amount || "",
            net_area: u.pivot?.net_area || "",
            meter_cost: u.pivot?.meter_cost || "",
        })),
        tenant_type: lease.tenant_type || "individual",
        rental_type: lease.rental_type || "detailed",
        start_date: lease.start_date?.split("T")[0] || "",
        end_date: lease.end_date?.split("T")[0] || "",
        handover_date: lease.handover_date?.split("T")[0] || "",
        rental_total_amount: lease.rental_total_amount || "",
        security_deposit_amount: lease.security_deposit_amount || "",
        security_deposit_due_date:
            lease.security_deposit_due_date?.split("T")[0] || "",
        number_of_years: lease.number_of_years || 0,
        number_of_months: lease.number_of_months || 0,
        number_of_days: lease.number_of_days || 0,
        free_period: lease.free_period || 0,
        terms_conditions: lease.terms_conditions || "",
        is_terms: lease.is_terms || false,
    });

    const filteredBuildings = data.community_id
        ? buildings.filter((b) => b.community_id === Number(data.community_id))
        : buildings;

    const filteredUnits = data.building_id
        ? units.filter((u) => u.building_id === Number(data.building_id))
        : units;

    const handleUnitToggle = (unitId: number) => {
        const exists = data.units.find((u) => u.id === unitId);

        if (exists) {
            setData(
                "units",
                data.units.filter((u) => u.id !== unitId),
            );
        } else {
            setData("units", [
                ...data.units,
                {
                    id: unitId,
                    annual_rental_amount: "",
                    net_area: "",
                    meter_cost: "",
                },
            ]);
        }
    };

    const handleUnitDataChange = (
        unitId: number,
        field: string,
        value: string,
    ) => {
        setData(
            "units",
            data.units.map((u) => {
                if (u.id === unitId) {
                    return { ...u, [field]: value };
                }

                return u;
            }),
        );
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(`/leases/${lease.id}`);
    };

    return (
        <>
            <Head
                title={`Edit Lease ${lease.contract_number || `#${lease.id}`}`}
            />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link href={`/leases/${lease.id}`}>
                            <Button variant="ghost" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-2xl font-bold tracking-tight">
                                Edit Lease{" "}
                                {lease.contract_number || `#${lease.id}`}
                            </h1>
                            <p className="text-muted-foreground">
                                Update lease contract details
                            </p>
                        </div>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="grid gap-6 lg:grid-cols-3">
                        {/* Main Content */}
                        <div className="space-y-6 lg:col-span-2">
                            {/* Basic Information */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <FileText className="h-5 w-5" />
                                        Basic Information
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="grid gap-4 sm:grid-cols-2">
                                        <div className="space-y-2">
                                            <Label htmlFor="contract_number">
                                                Contract Number
                                            </Label>
                                            <Input
                                                id="contract_number"
                                                value={data.contract_number}
                                                onChange={(e) =>
                                                    setData(
                                                        "contract_number",
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="Auto-generated if empty"
                                            />
                                            {errors.contract_number && (
                                                <p className="text-sm text-destructive">
                                                    {errors.contract_number}
                                                </p>
                                            )}
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="status_id">
                                                Status
                                            </Label>
                                            <Select
                                                value={String(data.status_id)}
                                                onValueChange={(value) =>
                                                    setData("status_id", value)
                                                }
                                            >
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select status" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {statuses.map((status) => (
                                                        <SelectItem
                                                            key={status.id}
                                                            value={String(
                                                                status.id,
                                                            )}
                                                        >
                                                            {status.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            {errors.status_id && (
                                                <p className="text-sm text-destructive">
                                                    {errors.status_id}
                                                </p>
                                            )}
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="tenant_type">
                                                Tenant Type
                                            </Label>
                                            <Select
                                                value={data.tenant_type}
                                                onValueChange={(value) =>
                                                    setData(
                                                        "tenant_type",
                                                        value,
                                                    )
                                                }
                                            >
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select type" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="individual">
                                                        Individual
                                                    </SelectItem>
                                                    <SelectItem value="company">
                                                        Company
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                            {errors.tenant_type && (
                                                <p className="text-sm text-destructive">
                                                    {errors.tenant_type}
                                                </p>
                                            )}
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="rental_type">
                                                Rental Type
                                            </Label>
                                            <Select
                                                value={data.rental_type}
                                                onValueChange={(value) =>
                                                    setData(
                                                        "rental_type",
                                                        value,
                                                    )
                                                }
                                            >
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select type" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="summary">
                                                        Summary
                                                    </SelectItem>
                                                    <SelectItem value="detailed">
                                                        Detailed
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                            {errors.rental_type && (
                                                <p className="text-sm text-destructive">
                                                    {errors.rental_type}
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Tenant Selection */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <User className="h-5 w-5" />
                                        Tenant
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-2">
                                        <Label htmlFor="tenant_id">
                                            Select Tenant
                                        </Label>
                                        <Select
                                            value={String(data.tenant_id)}
                                            onValueChange={(value) =>
                                                setData("tenant_id", value)
                                            }
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select tenant" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {tenants.map((tenant) => (
                                                    <SelectItem
                                                        key={tenant.id}
                                                        value={String(
                                                            tenant.id,
                                                        )}
                                                    >
                                                        {tenant.name}
                                                        {tenant.email &&
                                                            ` (${tenant.email})`}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        {errors.tenant_id && (
                                            <p className="text-sm text-destructive">
                                                {errors.tenant_id}
                                            </p>
                                        )}
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Property & Units */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Building2 className="h-5 w-5" />
                                        Property & Units
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="grid gap-4 sm:grid-cols-2">
                                        <div className="space-y-2">
                                            <Label htmlFor="community_id">
                                                Community
                                            </Label>
                                            <Select
                                                value={String(
                                                    data.community_id,
                                                )}
                                                onValueChange={(value) => {
                                                    setData(
                                                        "community_id",
                                                        value,
                                                    );
                                                    setData("building_id", "");
                                                }}
                                            >
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select community" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {communities.map(
                                                        (community) => (
                                                            <SelectItem
                                                                key={
                                                                    community.id
                                                                }
                                                                value={String(
                                                                    community.id,
                                                                )}
                                                            >
                                                                {community.name}
                                                            </SelectItem>
                                                        ),
                                                    )}
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="building_id">
                                                Building
                                            </Label>
                                            <Select
                                                value={String(data.building_id)}
                                                onValueChange={(value) =>
                                                    setData(
                                                        "building_id",
                                                        value,
                                                    )
                                                }
                                            >
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select building" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {filteredBuildings.map(
                                                        (building) => (
                                                            <SelectItem
                                                                key={
                                                                    building.id
                                                                }
                                                                value={String(
                                                                    building.id,
                                                                )}
                                                            >
                                                                {building.name}
                                                            </SelectItem>
                                                        ),
                                                    )}
                                                </SelectContent>
                                            </Select>
                                        </div>
                                    </div>

                                    {/* Units */}
                                    <div className="space-y-2">
                                        <Label>Units</Label>
                                        <div className="rounded-md border">
                                            {filteredUnits.length === 0 ? (
                                                <p className="p-4 text-sm text-muted-foreground">
                                                    Select a building to see
                                                    available units
                                                </p>
                                            ) : (
                                                <div className="divide-y">
                                                    {filteredUnits.map(
                                                        (unit) => {
                                                            const isSelected =
                                                                data.units.some(
                                                                    (u) =>
                                                                        u.id ===
                                                                        unit.id,
                                                                );
                                                            const selectedUnit =
                                                                data.units.find(
                                                                    (u) =>
                                                                        u.id ===
                                                                        unit.id,
                                                                );

                                                            return (
                                                                <div
                                                                    key={
                                                                        unit.id
                                                                    }
                                                                    className="p-3"
                                                                >
                                                                    <div className="flex items-center gap-3">
                                                                        <Checkbox
                                                                            id={`unit-${unit.id}`}
                                                                            checked={
                                                                                isSelected
                                                                            }
                                                                            onCheckedChange={() =>
                                                                                handleUnitToggle(
                                                                                    unit.id,
                                                                                )
                                                                            }
                                                                        />
                                                                        <Label
                                                                            htmlFor={`unit-${unit.id}`}
                                                                            className="flex-1 cursor-pointer"
                                                                        >
                                                                            <div className="flex items-center gap-2">
                                                                                <Home className="h-4 w-4" />
                                                                                <span className="font-medium">
                                                                                    {
                                                                                        unit.name
                                                                                    }
                                                                                </span>
                                                                            </div>
                                                                        </Label>
                                                                    </div>
                                                                    {isSelected && (
                                                                        <div className="mt-3 grid gap-3 pl-7 sm:grid-cols-3">
                                                                            <div className="space-y-1">
                                                                                <Label className="text-xs">
                                                                                    Annual
                                                                                    Rent
                                                                                </Label>
                                                                                <Input
                                                                                    type="number"
                                                                                    value={
                                                                                        selectedUnit?.annual_rental_amount ||
                                                                                        ""
                                                                                    }
                                                                                    onChange={(
                                                                                        e,
                                                                                    ) =>
                                                                                        handleUnitDataChange(
                                                                                            unit.id,
                                                                                            "annual_rental_amount",
                                                                                            e
                                                                                                .target
                                                                                                .value,
                                                                                        )
                                                                                    }
                                                                                    placeholder="0.00"
                                                                                />
                                                                            </div>
                                                                            <div className="space-y-1">
                                                                                <Label className="text-xs">
                                                                                    Net
                                                                                    Area
                                                                                    (sqm)
                                                                                </Label>
                                                                                <Input
                                                                                    type="number"
                                                                                    value={
                                                                                        selectedUnit?.net_area ||
                                                                                        ""
                                                                                    }
                                                                                    onChange={(
                                                                                        e,
                                                                                    ) =>
                                                                                        handleUnitDataChange(
                                                                                            unit.id,
                                                                                            "net_area",
                                                                                            e
                                                                                                .target
                                                                                                .value,
                                                                                        )
                                                                                    }
                                                                                    placeholder="0"
                                                                                />
                                                                            </div>
                                                                            <div className="space-y-1">
                                                                                <Label className="text-xs">
                                                                                    Meter
                                                                                    Cost
                                                                                </Label>
                                                                                <Input
                                                                                    type="number"
                                                                                    value={
                                                                                        selectedUnit?.meter_cost ||
                                                                                        ""
                                                                                    }
                                                                                    onChange={(
                                                                                        e,
                                                                                    ) =>
                                                                                        handleUnitDataChange(
                                                                                            unit.id,
                                                                                            "meter_cost",
                                                                                            e
                                                                                                .target
                                                                                                .value,
                                                                                        )
                                                                                    }
                                                                                    placeholder="0.00"
                                                                                />
                                                                            </div>
                                                                        </div>
                                                                    )}
                                                                </div>
                                                            );
                                                        },
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                        {errors.units && (
                                            <p className="text-sm text-destructive">
                                                {errors.units}
                                            </p>
                                        )}
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Terms & Conditions */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Terms & Conditions</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Textarea
                                            value={data.terms_conditions}
                                            onChange={(e) =>
                                                setData(
                                                    "terms_conditions",
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="Enter lease terms and conditions..."
                                            rows={6}
                                        />
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Checkbox
                                            id="is_terms"
                                            checked={data.is_terms}
                                            onCheckedChange={(checked) =>
                                                setData(
                                                    "is_terms",
                                                    checked as boolean,
                                                )
                                            }
                                        />
                                        <Label
                                            htmlFor="is_terms"
                                            className="cursor-pointer"
                                        >
                                            Terms and conditions have been
                                            agreed
                                        </Label>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Sidebar */}
                        <div className="space-y-6">
                            {/* Dates */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Calendar className="h-5 w-5" />
                                        Lease Period
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="start_date">
                                            Start Date
                                        </Label>
                                        <Input
                                            id="start_date"
                                            type="date"
                                            value={data.start_date}
                                            onChange={(e) =>
                                                setData(
                                                    "start_date",
                                                    e.target.value,
                                                )
                                            }
                                        />
                                        {errors.start_date && (
                                            <p className="text-sm text-destructive">
                                                {errors.start_date}
                                            </p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="end_date">
                                            End Date
                                        </Label>
                                        <Input
                                            id="end_date"
                                            type="date"
                                            value={data.end_date}
                                            onChange={(e) =>
                                                setData(
                                                    "end_date",
                                                    e.target.value,
                                                )
                                            }
                                        />
                                        {errors.end_date && (
                                            <p className="text-sm text-destructive">
                                                {errors.end_date}
                                            </p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="handover_date">
                                            Handover Date
                                        </Label>
                                        <Input
                                            id="handover_date"
                                            type="date"
                                            value={data.handover_date}
                                            onChange={(e) =>
                                                setData(
                                                    "handover_date",
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </div>

                                    <div className="grid grid-cols-3 gap-2">
                                        <div className="space-y-1">
                                            <Label className="text-xs">
                                                Years
                                            </Label>
                                            <Input
                                                type="number"
                                                min="0"
                                                value={data.number_of_years}
                                                onChange={(e) =>
                                                    setData(
                                                        "number_of_years",
                                                        parseInt(
                                                            e.target.value,
                                                        ) || 0,
                                                    )
                                                }
                                            />
                                        </div>
                                        <div className="space-y-1">
                                            <Label className="text-xs">
                                                Months
                                            </Label>
                                            <Input
                                                type="number"
                                                min="0"
                                                max="11"
                                                value={data.number_of_months}
                                                onChange={(e) =>
                                                    setData(
                                                        "number_of_months",
                                                        parseInt(
                                                            e.target.value,
                                                        ) || 0,
                                                    )
                                                }
                                            />
                                        </div>
                                        <div className="space-y-1">
                                            <Label className="text-xs">
                                                Days
                                            </Label>
                                            <Input
                                                type="number"
                                                min="0"
                                                max="30"
                                                value={data.number_of_days}
                                                onChange={(e) =>
                                                    setData(
                                                        "number_of_days",
                                                        parseInt(
                                                            e.target.value,
                                                        ) || 0,
                                                    )
                                                }
                                            />
                                        </div>
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="free_period">
                                            Free Period (days)
                                        </Label>
                                        <Input
                                            id="free_period"
                                            type="number"
                                            min="0"
                                            value={data.free_period}
                                            onChange={(e) =>
                                                setData(
                                                    "free_period",
                                                    parseInt(e.target.value) ||
                                                        0,
                                                )
                                            }
                                        />
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Financial */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <DollarSign className="h-5 w-5" />
                                        Financial
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="rental_total_amount">
                                            Total Rental Amount
                                        </Label>
                                        <Input
                                            id="rental_total_amount"
                                            type="number"
                                            step="0.01"
                                            value={data.rental_total_amount}
                                            onChange={(e) =>
                                                setData(
                                                    "rental_total_amount",
                                                    e.target.value,
                                                )
                                            }
                                        />
                                        {errors.rental_total_amount && (
                                            <p className="text-sm text-destructive">
                                                {errors.rental_total_amount}
                                            </p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="security_deposit_amount">
                                            Security Deposit
                                        </Label>
                                        <Input
                                            id="security_deposit_amount"
                                            type="number"
                                            step="0.01"
                                            value={data.security_deposit_amount}
                                            onChange={(e) =>
                                                setData(
                                                    "security_deposit_amount",
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="security_deposit_due_date">
                                            Deposit Due Date
                                        </Label>
                                        <Input
                                            id="security_deposit_due_date"
                                            type="date"
                                            value={
                                                data.security_deposit_due_date
                                            }
                                            onChange={(e) =>
                                                setData(
                                                    "security_deposit_due_date",
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Actions */}
                            <Card>
                                <CardContent className="pt-6">
                                    <div className="flex flex-col gap-2">
                                        <Button
                                            type="submit"
                                            disabled={processing}
                                        >
                                            <Save className="mr-2 h-4 w-4" />
                                            {processing
                                                ? "Saving..."
                                                : "Save Changes"}
                                        </Button>
                                        <Link href={`/leases/${lease.id}`}>
                                            <Button
                                                type="button"
                                                variant="outline"
                                                className="w-full"
                                            >
                                                Cancel
                                            </Button>
                                        </Link>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </form>
            </div>
        </>
    );
}

LeaseEdit.layout = {
    breadcrumbs: [
        { title: "Leases", href: "/leases" },
        { title: "Edit", href: "" },
    ],
};
