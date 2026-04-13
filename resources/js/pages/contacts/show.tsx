import { Head, Link, router } from "@inertiajs/react";
import { Edit, Mail, Phone, Trash2, User } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import {
    index as contactsIndex,
    edit as contactsEdit,
    destroy as contactsDestroy,
} from "@/routes/contacts";

interface Contact {
    id: number;
    contact_type: string;
    first_name: string;
    last_name: string;
    name: string;
    email: string;
    phone_number: string;
    national_phone_number: string;
    national_id?: string;
    gender?: string;
    active: boolean;
    created_at: string;
}

interface Props {
    contact: Contact;
}

export default function ContactShow({ contact }: Props) {
    const handleDelete = () => {
        router.delete(contactsDestroy({ contact: contact.id }));
    };

    const getContactTypeLabel = (type: string) => {
        return type.charAt(0).toUpperCase() + type.slice(1);
    };

    return (
        <>
            <Head title={contact.name} />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">{contact.name}</h1>
                        <div className="text-muted-foreground flex items-center gap-2">
                            <User className="h-4 w-4" />
                            {getContactTypeLabel(contact.contact_type)}
                        </div>
                    </div>
                    <div className="flex gap-2">
                        <Button variant="outline" asChild>
                            <Link href={contactsEdit({ contact: contact.id })}>
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
                                    <DialogTitle>Delete Contact</DialogTitle>
                                    <DialogDescription>
                                        Are you sure you want to delete this
                                        contact? This action cannot be undone.
                                    </DialogDescription>
                                </DialogHeader>
                                <DialogFooter>
                                    <Button variant="outline">Cancel</Button>
                                    <Button
                                        variant="destructive"
                                        onClick={handleDelete}
                                    >
                                        Delete
                                    </Button>
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
                            <Badge
                                variant={
                                    contact.active ? "default" : "secondary"
                                }
                            >
                                {contact.active ? "Active" : "Inactive"}
                            </Badge>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Contact Type</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {getContactTypeLabel(contact.contact_type)}
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Created</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-sm">
                                {new Date(
                                    contact.created_at,
                                ).toLocaleDateString()}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Contact Information */}
                <Card>
                    <CardHeader>
                        <CardTitle>Contact Information</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="flex items-center gap-3">
                            <Mail className="text-muted-foreground h-5 w-5" />
                            <div>
                                <div className="text-muted-foreground text-sm">
                                    Email
                                </div>
                                <div>{contact.email}</div>
                            </div>
                        </div>
                        <div className="flex items-center gap-3">
                            <Phone className="text-muted-foreground h-5 w-5" />
                            <div>
                                <div className="text-muted-foreground text-sm">
                                    Phone
                                </div>
                                <div>{contact.phone_number}</div>
                            </div>
                        </div>
                        {contact.national_id && (
                            <div className="flex items-center gap-3">
                                <User className="text-muted-foreground h-5 w-5" />
                                <div>
                                    <div className="text-muted-foreground text-sm">
                                        National ID
                                    </div>
                                    <div>{contact.national_id}</div>
                                </div>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

ContactShow.layout = {
    breadcrumbs: [{ title: "Contacts", href: contactsIndex() }],
};
