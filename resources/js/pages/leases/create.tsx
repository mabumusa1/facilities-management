import { Head, router, useForm } from "@inertiajs/react";
import {
    ArrowLeft,
    ArrowRight,
    Check,
    Building2,
    User,
    FileText,
    Eye,
} from "lucide-react";
import type { FormEventHandler } from "react";
import { useState } from "react";
import { Badge } from "@/components/ui/badge";
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
import { Textarea } from "@/components/ui/textarea";

interface Unit {
    id: number;
    name: string;
    building_id: number;
    building?: { name: string };
    community?: { name: string };
}

interface Contact {
    id: number;
    name: string;
    email: string | null;
    phone: string | null;
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

interface LeaseCreateProps {
    step: number;
    communities: Community[];
    buildings: Building[];
    availableUnits: Unit[];
    tenants: Contact[];
    wizardData: Record<string, unknown>;
}

interface LeaseFormData {
    tenant_id: number | null;
    community_id: number | null;
    building_id: number | null;
    units: { id: number; annual_rental_amount: number | null }[];
    tenant_type: "individual" | "company";
    rental_total_amount: number;
    security_deposit_amount: number;
    start_date: string;
    end_date: string;
    handover_date: string;
    terms_conditions: string;
}

const WIZARD_STEPS = [
    {
        id: 1,
        title: "Select Units",
        icon: Building2,
        description: "Choose units for the lease",
    },
    {
        id: 2,
        title: "Select Tenant",
        icon: User,
        description: "Assign a tenant",
    },
    {
        id: 3,
        title: "Lease Terms",
        icon: FileText,
        description: "Configure lease details",
    },
    { id: 4, title: "Review", icon: Eye, description: "Review and confirm" },
];

function StepIndicator({ currentStep }: { currentStep: number }) {
    return (
        <div className="flex items-center justify-center mb-8">
            {WIZARD_STEPS.map((step, index) => (
                <div key={step.id} className="flex items-center">
                    <div
                        className={`flex items-center justify-center w-10 h-10 rounded-full border-2 ${
                            currentStep >= step.id
                                ? "bg-primary border-primary text-primary-foreground"
                                : "border-muted-foreground/30 text-muted-foreground"
                        }`}
                    >
                        {currentStep > step.id ? (
                            <Check className="h-5 w-5" />
                        ) : (
                            <step.icon className="h-5 w-5" />
                        )}
                    </div>
                    {index < WIZARD_STEPS.length - 1 && (
                        <div
                            className={`w-16 h-0.5 mx-2 ${
                                currentStep > step.id
                                    ? "bg-primary"
                                    : "bg-muted-foreground/30"
                            }`}
                        />
                    )}
                </div>
            ))}
        </div>
    );
}

function Step1Units({
    units,
    selectedUnits,
    onSelectUnit,
    buildings,
}: {
    units: Unit[];
    selectedUnits: number[];
    onSelectUnit: (unitId: number) => void;
    buildings: Building[];
}) {
    const [filterBuilding, setFilterBuilding] = useState<string>("all");

    const filteredUnits =
        filterBuilding === "all"
            ? units
            : units.filter((u) => u.building_id === parseInt(filterBuilding));

    return (
        <div className="space-y-4">
            <div className="flex items-center gap-4 mb-4">
                <Label>Filter by Building:</Label>
                <Select
                    value={filterBuilding}
                    onValueChange={setFilterBuilding}
                >
                    <SelectTrigger className="w-[200px]">
                        <SelectValue placeholder="All Buildings" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All Buildings</SelectItem>
                        {buildings.map((building) => (
                            <SelectItem
                                key={building.id}
                                value={building.id.toString()}
                            >
                                {building.name}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </div>

            {filteredUnits.length === 0 ? (
                <p className="text-muted-foreground text-center py-8">
                    No available units found.
                </p>
            ) : (
                <div className="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                    {filteredUnits.map((unit) => (
                        <Card
                            key={unit.id}
                            className={`cursor-pointer transition-all ${
                                selectedUnits.includes(unit.id)
                                    ? "ring-2 ring-primary bg-primary/5"
                                    : "hover:shadow-md"
                            }`}
                            onClick={() => onSelectUnit(unit.id)}
                        >
                            <CardContent className="p-4">
                                <div className="flex items-start justify-between">
                                    <div>
                                        <h4 className="font-medium">
                                            {unit.name}
                                        </h4>
                                        <p className="text-sm text-muted-foreground">
                                            {unit.building?.name ||
                                                "Unknown Building"}
                                        </p>
                                    </div>
                                    <Checkbox
                                        checked={selectedUnits.includes(
                                            unit.id,
                                        )}
                                        onCheckedChange={() =>
                                            onSelectUnit(unit.id)
                                        }
                                    />
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            )}

            {selectedUnits.length > 0 && (
                <div className="mt-4 p-3 bg-muted rounded-lg">
                    <p className="text-sm font-medium">
                        Selected: {selectedUnits.length} unit(s)
                    </p>
                </div>
            )}
        </div>
    );
}

function Step2Tenant({
    tenants,
    selectedTenant,
    tenantType,
    onSelectTenant,
    onTenantTypeChange,
}: {
    tenants: Contact[];
    selectedTenant: number | null;
    tenantType: string;
    onSelectTenant: (tenantId: number) => void;
    onTenantTypeChange: (type: "individual" | "company") => void;
}) {
    const [search, setSearch] = useState("");

    const filteredTenants = tenants.filter(
        (t) =>
            t.name.toLowerCase().includes(search.toLowerCase()) ||
            (t.email && t.email.toLowerCase().includes(search.toLowerCase())),
    );

    return (
        <div className="space-y-6">
            <div className="space-y-2">
                <Label>Tenant Type</Label>
                <Select
                    value={tenantType}
                    onValueChange={(v) =>
                        onTenantTypeChange(v as "individual" | "company")
                    }
                >
                    <SelectTrigger className="w-[200px]">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="individual">Individual</SelectItem>
                        <SelectItem value="company">Company</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div className="space-y-2">
                <Label>Search Tenant</Label>
                <Input
                    placeholder="Search by name or email..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                />
            </div>

            {filteredTenants.length === 0 ? (
                <p className="text-muted-foreground text-center py-8">
                    No tenants found.
                </p>
            ) : (
                <div className="grid gap-3 md:grid-cols-2">
                    {filteredTenants.map((tenant) => (
                        <Card
                            key={tenant.id}
                            className={`cursor-pointer transition-all ${
                                selectedTenant === tenant.id
                                    ? "ring-2 ring-primary bg-primary/5"
                                    : "hover:shadow-md"
                            }`}
                            onClick={() => onSelectTenant(tenant.id)}
                        >
                            <CardContent className="p-4">
                                <div className="flex items-start justify-between">
                                    <div>
                                        <h4 className="font-medium">
                                            {tenant.name}
                                        </h4>
                                        {tenant.email && (
                                            <p className="text-sm text-muted-foreground">
                                                {tenant.email}
                                            </p>
                                        )}
                                        {tenant.phone && (
                                            <p className="text-sm text-muted-foreground">
                                                {tenant.phone}
                                            </p>
                                        )}
                                    </div>
                                    <Checkbox
                                        checked={selectedTenant === tenant.id}
                                    />
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            )}
        </div>
    );
}

function Step3Terms({
    data,
    setData,
    errors,
}: {
    data: LeaseFormData;
    setData: (key: keyof LeaseFormData, value: unknown) => void;
    errors: Partial<Record<keyof LeaseFormData, string>>;
}) {
    return (
        <div className="space-y-6">
            <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="start_date">Start Date *</Label>
                    <Input
                        id="start_date"
                        type="date"
                        value={data.start_date}
                        onChange={(e) => setData("start_date", e.target.value)}
                    />
                    {errors.start_date && (
                        <p className="text-sm text-destructive">
                            {errors.start_date}
                        </p>
                    )}
                </div>
                <div className="space-y-2">
                    <Label htmlFor="end_date">End Date *</Label>
                    <Input
                        id="end_date"
                        type="date"
                        value={data.end_date}
                        onChange={(e) => setData("end_date", e.target.value)}
                    />
                    {errors.end_date && (
                        <p className="text-sm text-destructive">
                            {errors.end_date}
                        </p>
                    )}
                </div>
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="handover_date">Handover Date</Label>
                    <Input
                        id="handover_date"
                        type="date"
                        value={data.handover_date}
                        onChange={(e) =>
                            setData("handover_date", e.target.value)
                        }
                    />
                </div>
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="rental_total_amount">
                        Total Rental Amount *
                    </Label>
                    <Input
                        id="rental_total_amount"
                        type="number"
                        min="0"
                        step="0.01"
                        value={data.rental_total_amount || ""}
                        onChange={(e) =>
                            setData(
                                "rental_total_amount",
                                parseFloat(e.target.value) || 0,
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
                        min="0"
                        step="0.01"
                        value={data.security_deposit_amount || ""}
                        onChange={(e) =>
                            setData(
                                "security_deposit_amount",
                                parseFloat(e.target.value) || 0,
                            )
                        }
                    />
                </div>
            </div>

            <div className="space-y-2">
                <Label htmlFor="terms_conditions">Terms & Conditions</Label>
                <Textarea
                    id="terms_conditions"
                    value={data.terms_conditions}
                    onChange={(e) =>
                        setData("terms_conditions", e.target.value)
                    }
                    rows={5}
                    placeholder="Enter any special terms or conditions..."
                />
            </div>
        </div>
    );
}

function Step4Review({
    data,
    units,
    tenants,
}: {
    data: LeaseFormData;
    units: Unit[];
    tenants: Contact[];
}) {
    const selectedUnits = units.filter((u) =>
        data.units.some((su) => su.id === u.id),
    );
    const selectedTenant = tenants.find((t) => t.id === data.tenant_id);

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle className="text-lg">Units</CardTitle>
                </CardHeader>
                <CardContent>
                    {selectedUnits.length === 0 ? (
                        <p className="text-muted-foreground">
                            No units selected
                        </p>
                    ) : (
                        <div className="flex flex-wrap gap-2">
                            {selectedUnits.map((unit) => (
                                <Badge key={unit.id} variant="secondary">
                                    {unit.name} ({unit.building?.name})
                                </Badge>
                            ))}
                        </div>
                    )}
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle className="text-lg">Tenant</CardTitle>
                </CardHeader>
                <CardContent>
                    {selectedTenant ? (
                        <div>
                            <p className="font-medium">{selectedTenant.name}</p>
                            <p className="text-sm text-muted-foreground">
                                Type: {data.tenant_type}
                            </p>
                            {selectedTenant.email && (
                                <p className="text-sm text-muted-foreground">
                                    {selectedTenant.email}
                                </p>
                            )}
                        </div>
                    ) : (
                        <p className="text-muted-foreground">
                            No tenant selected
                        </p>
                    )}
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle className="text-lg">Lease Terms</CardTitle>
                </CardHeader>
                <CardContent className="space-y-2">
                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <p className="text-sm text-muted-foreground">
                                Start Date
                            </p>
                            <p className="font-medium">
                                {data.start_date || "-"}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-muted-foreground">
                                End Date
                            </p>
                            <p className="font-medium">
                                {data.end_date || "-"}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-muted-foreground">
                                Total Rental Amount
                            </p>
                            <p className="font-medium">
                                $
                                {data.rental_total_amount?.toLocaleString() ||
                                    "0"}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-muted-foreground">
                                Security Deposit
                            </p>
                            <p className="font-medium">
                                $
                                {data.security_deposit_amount?.toLocaleString() ||
                                    "0"}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    );
}

export default function LeaseCreate({
    step,
    communities: _communities,
    buildings,
    availableUnits,
    tenants,
    wizardData,
}: LeaseCreateProps) {
    const [currentStep, setCurrentStep] = useState(step);

    const { data, setData, post, processing, errors } = useForm<LeaseFormData>({
        tenant_id:
            (wizardData?.step_2 as { tenant_id?: number })?.tenant_id || null,
        community_id: null,
        building_id: null,
        units:
            (
                wizardData?.step_1 as {
                    units?: {
                        id: number;
                        annual_rental_amount: number | null;
                    }[];
                }
            )?.units || [],
        tenant_type:
            (wizardData?.step_2 as { tenant_type?: "individual" | "company" })
                ?.tenant_type || "individual",
        rental_total_amount:
            (wizardData?.step_3 as { rental_total_amount?: number })
                ?.rental_total_amount || 0,
        security_deposit_amount:
            (wizardData?.step_3 as { security_deposit_amount?: number })
                ?.security_deposit_amount || 0,
        start_date:
            (wizardData?.step_3 as { start_date?: string })?.start_date || "",
        end_date: (wizardData?.step_3 as { end_date?: string })?.end_date || "",
        handover_date:
            (wizardData?.step_3 as { handover_date?: string })?.handover_date ||
            "",
        terms_conditions:
            (wizardData?.step_3 as { terms_conditions?: string })
                ?.terms_conditions || "",
    });

    const handleSelectUnit = (unitId: number) => {
        const current = data.units || [];
        const exists = current.some((u) => u.id === unitId);

        if (exists) {
            setData(
                "units",
                current.filter((u) => u.id !== unitId),
            );
        } else {
            setData("units", [
                ...current,
                { id: unitId, annual_rental_amount: null },
            ]);
        }
    };

    const handleNext = () => {
        // Save step data to session
        fetch("/leases/wizard/save-step", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content") || "",
            },
            body: JSON.stringify({
                step: currentStep,
                data:
                    currentStep === 1
                        ? { units: data.units }
                        : currentStep === 2
                          ? {
                                tenant_id: data.tenant_id,
                                tenant_type: data.tenant_type,
                            }
                          : {
                                start_date: data.start_date,
                                end_date: data.end_date,
                                handover_date: data.handover_date,
                                rental_total_amount: data.rental_total_amount,
                                security_deposit_amount:
                                    data.security_deposit_amount,
                                terms_conditions: data.terms_conditions,
                            },
            }),
        });

        setCurrentStep((prev) => Math.min(prev + 1, 4));
    };

    const handlePrev = () => {
        setCurrentStep((prev) => Math.max(prev - 1, 1));
    };

    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault();
        post("/leases");
    };

    const canProceed = () => {
        if (currentStep === 1) {
            return data.units.length > 0;
        }

        if (currentStep === 2) {
            return data.tenant_id !== null;
        }

        if (currentStep === 3) {
            return (
                data.start_date && data.end_date && data.rental_total_amount > 0
            );
        }

        return true;
    };

    return (
        <>
            <Head title="Create Lease" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                <div className="flex items-center gap-4">
                    <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => router.visit("/leases")}
                    >
                        <ArrowLeft className="h-4 w-4" />
                    </Button>
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Create New Lease
                        </h1>
                        <p className="text-muted-foreground">
                            Step {currentStep} of 4:{" "}
                            {WIZARD_STEPS[currentStep - 1].description}
                        </p>
                    </div>
                </div>

                <StepIndicator currentStep={currentStep} />

                <form onSubmit={handleSubmit}>
                    <Card>
                        <CardHeader>
                            <CardTitle>
                                {WIZARD_STEPS[currentStep - 1].title}
                            </CardTitle>
                            <CardDescription>
                                {WIZARD_STEPS[currentStep - 1].description}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {currentStep === 1 && (
                                <Step1Units
                                    units={availableUnits}
                                    selectedUnits={data.units.map((u) => u.id)}
                                    onSelectUnit={handleSelectUnit}
                                    buildings={buildings}
                                />
                            )}
                            {currentStep === 2 && (
                                <Step2Tenant
                                    tenants={tenants}
                                    selectedTenant={data.tenant_id}
                                    tenantType={data.tenant_type}
                                    onSelectTenant={(id) =>
                                        setData("tenant_id", id)
                                    }
                                    onTenantTypeChange={(type) =>
                                        setData("tenant_type", type)
                                    }
                                />
                            )}
                            {currentStep === 3 && (
                                <Step3Terms
                                    data={data}
                                    setData={setData}
                                    errors={errors}
                                />
                            )}
                            {currentStep === 4 && (
                                <Step4Review
                                    data={data}
                                    units={availableUnits}
                                    tenants={tenants}
                                />
                            )}
                        </CardContent>
                    </Card>

                    <div className="flex justify-between mt-6">
                        <Button
                            type="button"
                            variant="outline"
                            onClick={handlePrev}
                            disabled={currentStep === 1}
                        >
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Previous
                        </Button>

                        {currentStep < 4 ? (
                            <Button
                                type="button"
                                onClick={handleNext}
                                disabled={!canProceed()}
                            >
                                Next
                                <ArrowRight className="ml-2 h-4 w-4" />
                            </Button>
                        ) : (
                            <Button
                                type="submit"
                                disabled={processing || !canProceed()}
                            >
                                <Check className="mr-2 h-4 w-4" />
                                Create Lease
                            </Button>
                        )}
                    </div>
                </form>
            </div>
        </>
    );
}

LeaseCreate.layout = {
    breadcrumbs: [
        { title: "Leases", href: "/leases" },
        { title: "Create", href: "/leases/create" },
    ],
};
