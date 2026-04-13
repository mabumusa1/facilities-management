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
import { index as contactsIndex, update as contactsUpdate } from '@/routes/contacts';

interface Contact {
    id: number;
    contact_type: string;
    first_name: string;
    last_name: string;
    name: string;
    email: string;
    phone_number: string;
    national_phone_number: string;
    phone_country_code: string;
    gender?: string;
    national_id?: string;
    nationality?: string;
    georgian_birthdate?: string;
    active: boolean;
}

interface Props {
    contact: Contact;
}

export default function ContactEdit({ contact }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        first_name: contact.first_name,
        last_name: contact.last_name,
        email: contact.email,
        phone_number: contact.phone_number,
        national_phone_number: contact.national_phone_number,
        phone_country_code: contact.phone_country_code,
        gender: contact.gender ?? '',
        national_id: contact.national_id ?? '',
        nationality: contact.nationality ?? '',
        georgian_birthdate: contact.georgian_birthdate ?? '',
        active: contact.active,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(contactsUpdate({ contact: contact.id }));
    };

    return (
        <>
            <Head title={`Edit ${contact.name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div>
                    <h1 className="text-2xl font-bold">Edit Contact</h1>
                    <p className="text-muted-foreground">Update contact information for {contact.name}</p>
                </div>

                <Card className="max-w-2xl">
                    <CardHeader>
                        <CardTitle>Contact Details</CardTitle>
                        <CardDescription>Update the contact information</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="first_name">First Name</Label>
                                    <Input
                                        id="first_name"
                                        value={data.first_name}
                                        onChange={(e) => setData('first_name', e.target.value)}
                                        placeholder="Enter first name"
                                    />
                                    {errors.first_name && (
                                        <p className="text-destructive text-sm">{errors.first_name}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="last_name">Last Name</Label>
                                    <Input
                                        id="last_name"
                                        value={data.last_name}
                                        onChange={(e) => setData('last_name', e.target.value)}
                                        placeholder="Enter last name"
                                    />
                                    {errors.last_name && (
                                        <p className="text-destructive text-sm">{errors.last_name}</p>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    placeholder="Enter email address"
                                />
                                {errors.email && (
                                    <p className="text-destructive text-sm">{errors.email}</p>
                                )}
                            </div>

                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="phone_number">Phone Number (International)</Label>
                                    <Input
                                        id="phone_number"
                                        value={data.phone_number}
                                        onChange={(e) => setData('phone_number', e.target.value)}
                                        placeholder="+966500000000"
                                    />
                                    {errors.phone_number && (
                                        <p className="text-destructive text-sm">{errors.phone_number}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="national_phone_number">Phone Number (Local)</Label>
                                    <Input
                                        id="national_phone_number"
                                        value={data.national_phone_number}
                                        onChange={(e) => setData('national_phone_number', e.target.value)}
                                        placeholder="0500000000"
                                    />
                                    {errors.national_phone_number && (
                                        <p className="text-destructive text-sm">{errors.national_phone_number}</p>
                                    )}
                                </div>
                            </div>

                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="gender">Gender</Label>
                                    <Select value={data.gender} onValueChange={(v) => setData('gender', v)}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select gender" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="">Not specified</SelectItem>
                                            <SelectItem value="male">Male</SelectItem>
                                            <SelectItem value="female">Female</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {errors.gender && (
                                        <p className="text-destructive text-sm">{errors.gender}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="national_id">National ID</Label>
                                    <Input
                                        id="national_id"
                                        value={data.national_id}
                                        onChange={(e) => setData('national_id', e.target.value)}
                                        placeholder="Enter national ID"
                                    />
                                    {errors.national_id && (
                                        <p className="text-destructive text-sm">{errors.national_id}</p>
                                    )}
                                </div>
                            </div>

                            <div className="flex gap-4">
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Updating...' : 'Update Contact'}
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

ContactEdit.layout = {
    breadcrumbs: [
        { title: 'Contacts', href: contactsIndex() },
        { title: 'Edit', href: '#' },
    ],
};
