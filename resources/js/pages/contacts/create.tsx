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
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    index as contactsIndex,
    store as contactsStore,
} from "@/routes/contacts";

interface Props {
    contactType: string;
}

const managerRoleOptions = [
    { value: "Admins", label: "Admin" },
    { value: "accountingManagers", label: "Accounting Manager" },
    { value: "serviceManagers", label: "Service Manager" },
    { value: "marketingManagers", label: "Marketing Manager" },
    { value: "salesAndLeasingManagers", label: "Sales & Leasing Manager" },
] as const;

export default function ContactCreate({ contactType }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        contact_type: contactType,
        role: "",
        first_name: "",
        last_name: "",
        email: "",
        phone_number: "",
        national_phone_number: "",
        phone_country_code: "SA",
        gender: "",
        national_id: "",
        nationality: "",
        georgian_birthdate: "",
        active: true,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(contactsStore.url());
    };

    return (
        <>
            <Head title="Create Contact" />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div>
                    <h1 className="text-2xl font-bold">Create Contact</h1>
                    <p className="text-muted-foreground">
                        Add a new {data.contact_type}
                    </p>
                </div>

                <Card className="max-w-2xl">
                    <CardHeader>
                        <CardTitle>Contact Details</CardTitle>
                        <CardDescription>
                            Enter the contact information
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="contact_type">
                                    Contact Type
                                </Label>
                                <Select
                                    value={data.contact_type}
                                    onValueChange={(value) => {
                                        setData("contact_type", value);

                                        if (value !== "admin") {
                                            setData("role", "");
                                        }
                                    }}
                                >
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="owner">
                                            Owner
                                        </SelectItem>
                                        <SelectItem value="tenant">
                                            Tenant
                                        </SelectItem>
                                        <SelectItem value="admin">
                                            Admin
                                        </SelectItem>
                                        <SelectItem value="professional">
                                            Professional
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.contact_type && (
                                    <p className="text-destructive text-sm">
                                        {errors.contact_type}
                                    </p>
                                )}
                            </div>

                            {data.contact_type === "admin" && (
                                <div className="space-y-2">
                                    <Label htmlFor="role">Manager Role</Label>
                                    <Select
                                        value={data.role}
                                        onValueChange={(value) =>
                                            setData("role", value)
                                        }
                                    >
                                        <SelectTrigger id="role">
                                            <SelectValue placeholder="Select manager role" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {managerRoleOptions.map((option) => (
                                                <SelectItem
                                                    key={option.value}
                                                    value={option.value}
                                                >
                                                    {option.label}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.role && (
                                        <p className="text-destructive text-sm">
                                            {errors.role}
                                        </p>
                                    )}
                                </div>
                            )}

                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="first_name">
                                        First Name
                                    </Label>
                                    <Input
                                        id="first_name"
                                        value={data.first_name}
                                        onChange={(e) =>
                                            setData(
                                                "first_name",
                                                e.target.value,
                                            )
                                        }
                                        placeholder="Enter first name"
                                    />
                                    {errors.first_name && (
                                        <p className="text-destructive text-sm">
                                            {errors.first_name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="last_name">Last Name</Label>
                                    <Input
                                        id="last_name"
                                        value={data.last_name}
                                        onChange={(e) =>
                                            setData("last_name", e.target.value)
                                        }
                                        placeholder="Enter last name"
                                    />
                                    {errors.last_name && (
                                        <p className="text-destructive text-sm">
                                            {errors.last_name}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    onChange={(e) =>
                                        setData("email", e.target.value)
                                    }
                                    placeholder="Enter email address"
                                />
                                {errors.email && (
                                    <p className="text-destructive text-sm">
                                        {errors.email}
                                    </p>
                                )}
                            </div>

                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="phone_number">
                                        Phone Number (International)
                                    </Label>
                                    <Input
                                        id="phone_number"
                                        value={data.phone_number}
                                        onChange={(e) =>
                                            setData(
                                                "phone_number",
                                                e.target.value,
                                            )
                                        }
                                        placeholder="+966500000000"
                                    />
                                    {errors.phone_number && (
                                        <p className="text-destructive text-sm">
                                            {errors.phone_number}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="national_phone_number">
                                        Phone Number (Local)
                                    </Label>
                                    <Input
                                        id="national_phone_number"
                                        value={data.national_phone_number}
                                        onChange={(e) =>
                                            setData(
                                                "national_phone_number",
                                                e.target.value,
                                            )
                                        }
                                        placeholder="0500000000"
                                    />
                                    {errors.national_phone_number && (
                                        <p className="text-destructive text-sm">
                                            {errors.national_phone_number}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="flex gap-4">
                                <Button type="submit" disabled={processing}>
                                    {processing
                                        ? "Creating..."
                                        : "Create Contact"}
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

ContactCreate.layout = {
    breadcrumbs: [
        { title: "Contacts", href: contactsIndex() },
        { title: "Create", href: "#" },
    ],
};
