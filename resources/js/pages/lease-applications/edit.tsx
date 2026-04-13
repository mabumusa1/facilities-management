import { Head, Link, useForm } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { ArrowLeft, Save, User, Building2, DollarSign, Calendar, FileText } from 'lucide-react';
import { useState, useMemo } from 'react';

interface Community {
    id: number;
    name: string;
}

interface Building {
    id: number;
    name: string;
    community_id: number;
}

interface Unit {
    id: number;
    name: string;
    building_id?: number;
    community_id?: number;
    building?: {
        id: number;
        name: string;
    };
    community?: {
        id: number;
        name: string;
    };
}

interface Contact {
    id: number;
    name: string;
    email: string;
    phone: string;
}

interface Source {
    value: string;
    label: string;
}

interface ApplicationUnit {
    id: number;
    name: string;
    pivot?: {
        proposed_rental_amount: string | null;
        net_area: string | null;
        meter_cost: string | null;
    };
}

interface Application {
    id: number;
    application_number: string;
    applicant_id: number | null;
    applicant_name: string;
    applicant_email: string;
    applicant_phone: string | null;
    applicant_type: string;
    company_name: string | null;
    national_id: string | null;
    commercial_registration: string | null;
    community_id: number | null;
    building_id: number | null;
    units: ApplicationUnit[];
    quoted_rental_amount: string | null;
    security_deposit: string | null;
    proposed_start_date: string | null;
    proposed_end_date: string | null;
    proposed_duration_months: number | null;
    special_terms: string | null;
    notes: string | null;
    source: string | null;
    assigned_to_id: number | null;
    applicant?: Contact | null;
}

interface ApplicationEditProps {
    application: Application;
    communities: Community[];
    buildings: Building[];
    availableUnits: Unit[];
    contacts: Contact[];
    sources: Source[];
}

export default function ApplicationEdit({
    application,
    communities,
    buildings,
    availableUnits,
    contacts,
    sources,
}: ApplicationEditProps) {
    const [selectedUnits, setSelectedUnits] = useState<number[]>(
        application.units.map(u => u.id)
    );

    // Combine available units with currently selected units
    const allUnits = useMemo(() => {
        const unitMap = new Map<number, Unit>();
        availableUnits.forEach(u => unitMap.set(u.id, u));
        application.units.forEach(u => {
            if (!unitMap.has(u.id)) {
                unitMap.set(u.id, u as unknown as Unit);
            }
        });
        return Array.from(unitMap.values());
    }, [availableUnits, application.units]);

    const { data, setData, put, processing, errors } = useForm({
        applicant_id: application.applicant_id?.toString() || '',
        applicant_name: application.applicant_name || '',
        applicant_email: application.applicant_email || '',
        applicant_phone: application.applicant_phone || '',
        applicant_type: application.applicant_type || 'individual',
        company_name: application.company_name || '',
        national_id: application.national_id || '',
        commercial_registration: application.commercial_registration || '',
        community_id: application.community_id?.toString() || '',
        building_id: application.building_id?.toString() || '',
        units: application.units.map(u => ({
            id: u.id,
            proposed_rental_amount: u.pivot?.proposed_rental_amount ? parseFloat(u.pivot.proposed_rental_amount) : undefined,
            net_area: u.pivot?.net_area ? parseFloat(u.pivot.net_area) : undefined,
            meter_cost: u.pivot?.meter_cost ? parseFloat(u.pivot.meter_cost) : undefined,
        })),
        quoted_rental_amount: application.quoted_rental_amount || '',
        security_deposit: application.security_deposit || '',
        proposed_start_date: application.proposed_start_date ? application.proposed_start_date.split('T')[0] : '',
        proposed_end_date: application.proposed_end_date ? application.proposed_end_date.split('T')[0] : '',
        proposed_duration_months: application.proposed_duration_months?.toString() || '',
        special_terms: application.special_terms || '',
        notes: application.notes || '',
        source: application.source || '',
        assigned_to_id: application.assigned_to_id?.toString() || '',
    });

    // Filter buildings based on selected community
    const filteredBuildings = useMemo(() => {
        if (!data.community_id) return buildings;
        return buildings.filter(b => b.community_id === parseInt(data.community_id));
    }, [data.community_id, buildings]);

    // Filter units based on selected building
    const filteredUnits = useMemo(() => {
        if (!data.building_id) return allUnits;
        return allUnits.filter(u => u.building_id === parseInt(data.building_id) || u.building?.id === parseInt(data.building_id));
    }, [data.building_id, allUnits]);

    const handleUnitToggle = (unitId: number) => {
        const newSelected = selectedUnits.includes(unitId)
            ? selectedUnits.filter(id => id !== unitId)
            : [...selectedUnits, unitId];

        setSelectedUnits(newSelected);
        setData('units', newSelected.map(id => ({ id })));
    };

    const handleContactSelect = (contactId: string) => {
        setData('applicant_id', contactId);
        if (contactId) {
            const contact = contacts.find(c => c.id === parseInt(contactId));
            if (contact) {
                setData(prev => ({
                    ...prev,
                    applicant_id: contactId,
                    applicant_name: contact.name,
                    applicant_email: contact.email,
                    applicant_phone: contact.phone || '',
                }));
            }
        }
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(`/lease-applications/${application.id}`);
    };

    return (
        <>
            <Head title={`Edit Application ${application.application_number}`} />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link href={`/lease-applications/${application.id}`}>
                            <Button variant="ghost" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-2xl font-bold tracking-tight">Edit Application</h1>
                            <p className="text-muted-foreground">{application.application_number}</p>
                        </div>
                    </div>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="grid gap-6 lg:grid-cols-3">
                        {/* Main Form */}
                        <div className="space-y-6 lg:col-span-2">
                            {/* Applicant Information */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <User className="h-5 w-5" />
                                        Applicant Information
                                    </CardTitle>
                                    <CardDescription>Update the applicant's details</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="grid gap-4 sm:grid-cols-2">
                                        <div className="sm:col-span-2">
                                            <Label htmlFor="applicant_id">Link to Existing Contact</Label>
                                            <Select value={data.applicant_id} onValueChange={handleContactSelect}>
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select a contact or keep manual entry" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="">Manual Entry</SelectItem>
                                                    {contacts.map(contact => (
                                                        <SelectItem key={contact.id} value={contact.id.toString()}>
                                                            {contact.name} ({contact.email})
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        <div>
                                            <Label htmlFor="applicant_type">Applicant Type</Label>
                                            <Select value={data.applicant_type} onValueChange={(value) => setData('applicant_type', value)}>
                                                <SelectTrigger>
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="individual">Individual</SelectItem>
                                                    <SelectItem value="company">Company</SelectItem>
                                                </SelectContent>
                                            </Select>
                                            {errors.applicant_type && <p className="text-sm text-red-500 mt-1">{errors.applicant_type}</p>}
                                        </div>

                                        <div>
                                            <Label htmlFor="applicant_name">Full Name *</Label>
                                            <Input
                                                id="applicant_name"
                                                value={data.applicant_name}
                                                onChange={(e) => setData('applicant_name', e.target.value)}
                                            />
                                            {errors.applicant_name && <p className="text-sm text-red-500 mt-1">{errors.applicant_name}</p>}
                                        </div>

                                        <div>
                                            <Label htmlFor="applicant_email">Email *</Label>
                                            <Input
                                                id="applicant_email"
                                                type="email"
                                                value={data.applicant_email}
                                                onChange={(e) => setData('applicant_email', e.target.value)}
                                            />
                                            {errors.applicant_email && <p className="text-sm text-red-500 mt-1">{errors.applicant_email}</p>}
                                        </div>

                                        <div>
                                            <Label htmlFor="applicant_phone">Phone</Label>
                                            <Input
                                                id="applicant_phone"
                                                value={data.applicant_phone}
                                                onChange={(e) => setData('applicant_phone', e.target.value)}
                                            />
                                            {errors.applicant_phone && <p className="text-sm text-red-500 mt-1">{errors.applicant_phone}</p>}
                                        </div>

                                        {data.applicant_type === 'company' && (
                                            <>
                                                <div>
                                                    <Label htmlFor="company_name">Company Name *</Label>
                                                    <Input
                                                        id="company_name"
                                                        value={data.company_name}
                                                        onChange={(e) => setData('company_name', e.target.value)}
                                                    />
                                                    {errors.company_name && <p className="text-sm text-red-500 mt-1">{errors.company_name}</p>}
                                                </div>

                                                <div>
                                                    <Label htmlFor="commercial_registration">Commercial Registration</Label>
                                                    <Input
                                                        id="commercial_registration"
                                                        value={data.commercial_registration}
                                                        onChange={(e) => setData('commercial_registration', e.target.value)}
                                                    />
                                                </div>
                                            </>
                                        )}

                                        {data.applicant_type === 'individual' && (
                                            <div>
                                                <Label htmlFor="national_id">National ID</Label>
                                                <Input
                                                    id="national_id"
                                                    value={data.national_id}
                                                    onChange={(e) => setData('national_id', e.target.value)}
                                                />
                                            </div>
                                        )}
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Property Selection */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Building2 className="h-5 w-5" />
                                        Property Selection
                                    </CardTitle>
                                    <CardDescription>Update the property and units</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <Label htmlFor="community_id">Community</Label>
                                            <Select value={data.community_id} onValueChange={(value) => {
                                                setData(prev => ({ ...prev, community_id: value, building_id: '' }));
                                            }}>
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select a community" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="">All Communities</SelectItem>
                                                    {communities.map(community => (
                                                        <SelectItem key={community.id} value={community.id.toString()}>
                                                            {community.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        <div>
                                            <Label htmlFor="building_id">Building</Label>
                                            <Select value={data.building_id} onValueChange={(value) => setData('building_id', value)}>
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select a building" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="">All Buildings</SelectItem>
                                                    {filteredBuildings.map(building => (
                                                        <SelectItem key={building.id} value={building.id.toString()}>
                                                            {building.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                        </div>
                                    </div>

                                    {/* Units */}
                                    <div>
                                        <Label>Units</Label>
                                        <div className="mt-2 border rounded-lg p-4 max-h-60 overflow-y-auto">
                                            {filteredUnits.length === 0 ? (
                                                <p className="text-sm text-muted-foreground">No units found</p>
                                            ) : (
                                                <div className="grid gap-2 sm:grid-cols-2">
                                                    {filteredUnits.map(unit => (
                                                        <div key={unit.id} className="flex items-center space-x-2">
                                                            <Checkbox
                                                                id={`unit-${unit.id}`}
                                                                checked={selectedUnits.includes(unit.id)}
                                                                onCheckedChange={() => handleUnitToggle(unit.id)}
                                                            />
                                                            <label
                                                                htmlFor={`unit-${unit.id}`}
                                                                className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer"
                                                            >
                                                                {unit.name}
                                                                {unit.building && <span className="text-muted-foreground ml-1">({unit.building.name})</span>}
                                                            </label>
                                                        </div>
                                                    ))}
                                                </div>
                                            )}
                                        </div>
                                        {errors.units && <p className="text-sm text-red-500 mt-1">{errors.units}</p>}
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Lease Terms */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Calendar className="h-5 w-5" />
                                        Lease Terms
                                    </CardTitle>
                                    <CardDescription>Update the proposed lease terms</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <Label htmlFor="proposed_start_date">Proposed Start Date</Label>
                                            <Input
                                                id="proposed_start_date"
                                                type="date"
                                                value={data.proposed_start_date}
                                                onChange={(e) => setData('proposed_start_date', e.target.value)}
                                            />
                                            {errors.proposed_start_date && <p className="text-sm text-red-500 mt-1">{errors.proposed_start_date}</p>}
                                        </div>

                                        <div>
                                            <Label htmlFor="proposed_end_date">Proposed End Date</Label>
                                            <Input
                                                id="proposed_end_date"
                                                type="date"
                                                value={data.proposed_end_date}
                                                onChange={(e) => setData('proposed_end_date', e.target.value)}
                                            />
                                            {errors.proposed_end_date && <p className="text-sm text-red-500 mt-1">{errors.proposed_end_date}</p>}
                                        </div>

                                        <div>
                                            <Label htmlFor="proposed_duration_months">Duration (Months)</Label>
                                            <Input
                                                id="proposed_duration_months"
                                                type="number"
                                                value={data.proposed_duration_months}
                                                onChange={(e) => setData('proposed_duration_months', e.target.value)}
                                                min="1"
                                            />
                                            {errors.proposed_duration_months && <p className="text-sm text-red-500 mt-1">{errors.proposed_duration_months}</p>}
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Additional Details */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <FileText className="h-5 w-5" />
                                        Additional Details
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div>
                                        <Label htmlFor="special_terms">Special Terms & Conditions</Label>
                                        <Textarea
                                            id="special_terms"
                                            value={data.special_terms}
                                            onChange={(e) => setData('special_terms', e.target.value)}
                                            rows={4}
                                            placeholder="Enter any special terms or conditions..."
                                        />
                                    </div>

                                    <div>
                                        <Label htmlFor="notes">Internal Notes</Label>
                                        <Textarea
                                            id="notes"
                                            value={data.notes}
                                            onChange={(e) => setData('notes', e.target.value)}
                                            rows={3}
                                            placeholder="Enter any internal notes..."
                                        />
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Sidebar */}
                        <div className="space-y-6">
                            {/* Financial Details */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <DollarSign className="h-5 w-5" />
                                        Financial Details
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div>
                                        <Label htmlFor="quoted_rental_amount">Quoted Rental Amount</Label>
                                        <Input
                                            id="quoted_rental_amount"
                                            type="number"
                                            value={data.quoted_rental_amount}
                                            onChange={(e) => setData('quoted_rental_amount', e.target.value)}
                                            min="0"
                                            step="0.01"
                                        />
                                        {errors.quoted_rental_amount && <p className="text-sm text-red-500 mt-1">{errors.quoted_rental_amount}</p>}
                                    </div>

                                    <div>
                                        <Label htmlFor="security_deposit">Security Deposit</Label>
                                        <Input
                                            id="security_deposit"
                                            type="number"
                                            value={data.security_deposit}
                                            onChange={(e) => setData('security_deposit', e.target.value)}
                                            min="0"
                                            step="0.01"
                                        />
                                        {errors.security_deposit && <p className="text-sm text-red-500 mt-1">{errors.security_deposit}</p>}
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Application Source */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Application Source</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div>
                                        <Label htmlFor="source">Source</Label>
                                        <Select value={data.source} onValueChange={(value) => setData('source', value)}>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select source" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {sources.map(source => (
                                                    <SelectItem key={source.value} value={source.value}>
                                                        {source.label}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </div>

                                    <div>
                                        <Label htmlFor="assigned_to_id">Assigned To</Label>
                                        <Select value={data.assigned_to_id} onValueChange={(value) => setData('assigned_to_id', value)}>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select assignee" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="">Unassigned</SelectItem>
                                                {contacts.map(contact => (
                                                    <SelectItem key={contact.id} value={contact.id.toString()}>
                                                        {contact.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Actions */}
                            <Card>
                                <CardContent className="pt-6 space-y-2">
                                    <Button type="submit" className="w-full" disabled={processing}>
                                        <Save className="mr-2 h-4 w-4" />
                                        {processing ? 'Saving...' : 'Save Changes'}
                                    </Button>
                                    <Link href={`/lease-applications/${application.id}`} className="block">
                                        <Button type="button" variant="outline" className="w-full">
                                            Cancel
                                        </Button>
                                    </Link>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </form>
            </div>
        </>
    );
}

ApplicationEdit.layout = {
    breadcrumbs: [
        { title: 'Lease Applications', href: '/lease-applications' },
        { title: 'Edit', href: '' },
    ],
};
