import { Head, Link, router } from '@inertiajs/react';
import { Plus, Search, User, Users } from 'lucide-react';
import { useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { index as contactsIndex, create as contactsCreate, show as contactsShow } from '@/routes/contacts';

interface Contact {
    id: number;
    contact_type: 'owner' | 'tenant' | 'admin' | 'professional';
    first_name: string;
    last_name: string;
    name: string;
    email: string;
    phone_number: string;
    active: boolean;
    created_at: string;
}

interface PaginatedData {
    data: Contact[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

interface Props {
    contacts: PaginatedData;
    filters: {
        type?: string;
        search?: string;
        status?: string;
    };
}

export default function ContactsIndex({ contacts, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status ?? '');
    const contactType = filters.type ?? 'all';

    const handleSearch = () => {
        router.get(contactsIndex(), { type: contactType, search, status }, { preserveState: true });
    };

    const handleStatusChange = (value: string) => {
        setStatus(value);
        router.get(contactsIndex(), { type: contactType, search, status: value }, { preserveState: true });
    };

    const handleTabChange = (value: string) => {
        router.get(contactsIndex(), { type: value, search, status }, { preserveState: true });
    };

    const getContactTypeBadgeVariant = (type: string) => {
        switch (type) {
            case 'owner':
                return 'default';
            case 'tenant':
                return 'secondary';
            case 'admin':
                return 'outline';
            case 'professional':
                return 'destructive';
            default:
                return 'default';
        }
    };

    return (
        <>
            <Head title="Contacts" />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Contacts</h1>
                        <p className="text-muted-foreground">
                            Manage owners, tenants, admins, and professionals
                        </p>
                    </div>
                    <Button asChild>
                        <Link href={contactsCreate() + `?type=${contactType !== 'all' ? contactType : 'owner'}`}>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Contact
                        </Link>
                    </Button>
                </div>

                {/* Type Filter Tabs */}
                <div className="flex gap-2">
                    <Button
                        variant={contactType === 'all' ? 'default' : 'outline'}
                        onClick={() => handleTabChange('all')}
                    >
                        All
                    </Button>
                    <Button
                        variant={contactType === 'owner' ? 'default' : 'outline'}
                        onClick={() => handleTabChange('owner')}
                    >
                        Owners
                    </Button>
                    <Button
                        variant={contactType === 'tenant' ? 'default' : 'outline'}
                        onClick={() => handleTabChange('tenant')}
                    >
                        Tenants
                    </Button>
                    <Button
                        variant={contactType === 'admin' ? 'default' : 'outline'}
                        onClick={() => handleTabChange('admin')}
                    >
                        Admins
                    </Button>
                    <Button
                        variant={contactType === 'professional' ? 'default' : 'outline'}
                        onClick={() => handleTabChange('professional')}
                    >
                        Professionals
                    </Button>
                </div>

                <div className="space-y-4">
                        {/* Filters */}
                        <Card>
                            <CardContent className="pt-6">
                                <div className="flex gap-4">
                                    <div className="relative flex-1">
                                        <Search className="text-muted-foreground absolute top-2.5 left-2.5 h-4 w-4" />
                                        <Input
                                            placeholder="Search contacts..."
                                            value={search}
                                            onChange={(e) => setSearch(e.target.value)}
                                            onKeyDown={(e) => e.key === 'Enter' && handleSearch()}
                                            className="pl-8"
                                        />
                                    </div>
                                    <Select value={status} onValueChange={handleStatusChange}>
                                        <SelectTrigger className="w-40">
                                            <SelectValue placeholder="All statuses" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="">All statuses</SelectItem>
                                            <SelectItem value="active">Active</SelectItem>
                                            <SelectItem value="inactive">Inactive</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <Button onClick={handleSearch}>Search</Button>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Data Table */}
                        <Card>
                            <CardHeader>
                                <CardTitle>
                                    {contacts.total} {contacts.total === 1 ? 'Contact' : 'Contacts'}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                {contacts.data.length === 0 ? (
                                    <div className="text-muted-foreground py-8 text-center">
                                        No contacts found. Create your first contact to get started.
                                    </div>
                                ) : (
                                    <div className="divide-y">
                                        {contacts.data.map((contact) => (
                                            <Link
                                                key={contact.id}
                                                href={contactsShow({ contact: contact.id })}
                                                className="hover:bg-muted/50 flex items-center justify-between p-4 transition-colors"
                                            >
                                                <div className="flex items-center gap-4">
                                                    <div className="bg-primary/10 flex h-10 w-10 items-center justify-center rounded-lg">
                                                        {contact.contact_type === 'owner' || contact.contact_type === 'tenant' ? (
                                                            <User className="text-primary h-5 w-5" />
                                                        ) : (
                                                            <Users className="text-primary h-5 w-5" />
                                                        )}
                                                    </div>
                                                    <div>
                                                        <div className="font-medium">{contact.name}</div>
                                                        <div className="text-muted-foreground text-sm">
                                                            {contact.email} • {contact.phone_number}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="flex items-center gap-4">
                                                    {contactType === 'all' && (
                                                        <Badge variant={getContactTypeBadgeVariant(contact.contact_type)}>
                                                            {contact.contact_type}
                                                        </Badge>
                                                    )}
                                                    <Badge variant={contact.active ? 'default' : 'secondary'}>
                                                        {contact.active ? 'Active' : 'Inactive'}
                                                    </Badge>
                                                </div>
                                            </Link>
                                        ))}
                                    </div>
                                )}

                                {/* Pagination */}
                                {contacts.last_page > 1 && (
                                    <div className="mt-4 flex items-center justify-center gap-2">
                                        {contacts.links.map((link, index) => (
                                            <Button
                                                key={index}
                                                variant={link.active ? 'default' : 'outline'}
                                                size="sm"
                                                disabled={!link.url}
                                                onClick={() => link.url && router.get(link.url)}
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                            />
                                        ))}
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                </div>
            </div>
        </>
    );
}

ContactsIndex.layout = {
    breadcrumbs: [
        { title: 'Contacts', href: contactsIndex() },
    ],
};
