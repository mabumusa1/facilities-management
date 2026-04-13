import { Head, Link, router, useForm } from "@inertiajs/react";
import {
    FileText,
    Edit,
    Calendar,
    DollarSign,
    User,
    Building2,
    CheckCircle,
    Clock,
    XCircle,
    AlertCircle,
    ArrowLeft,
    Home,
    Send,
    Pause,
    Play,
    ArrowRight,
    History,
    Mail,
} from "lucide-react";
import { useState } from "react";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
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
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { Textarea } from "@/components/ui/textarea";

interface Unit {
    id: number;
    name: string;
    building?: {
        id: number;
        name: string;
    };
    pivot?: {
        proposed_rental_amount: string | null;
        net_area: string | null;
        meter_cost: string | null;
    };
}

interface Contact {
    id: number;
    name: string;
    email?: string;
    phone?: string;
}

interface Community {
    id: number;
    name: string;
}

interface Building {
    id: number;
    name: string;
}

interface Lease {
    id: number;
    contract_number: string | null;
}

interface StateHistoryItem {
    id: number;
    from_status: string | null;
    from_status_label: string;
    to_status: string;
    to_status_label: string;
    changed_by: string | null;
    notes: string | null;
    created_at: string;
}

interface Application {
    id: number;
    application_number: string;
    status: string;
    applicant: Contact | null;
    applicant_name: string;
    applicant_email: string;
    applicant_phone: string | null;
    applicant_type: string;
    company_name: string | null;
    national_id: string | null;
    commercial_registration: string | null;
    community: Community | null;
    building: Building | null;
    units: Unit[];
    quoted_rental_amount: string | null;
    security_deposit: string | null;
    proposed_start_date: string | null;
    proposed_end_date: string | null;
    proposed_duration_months: number | null;
    special_terms: string | null;
    notes: string | null;
    quote_sent_at: string | null;
    quote_expires_at: string | null;
    reviewed_by: Contact | null;
    reviewed_at: string | null;
    review_notes: string | null;
    rejection_reason: string | null;
    converted_lease: Lease | null;
    converted_at: string | null;
    created_by: Contact | null;
    assigned_to: Contact | null;
    source: string | null;
    created_at: string;
    updated_at: string;
}

interface ApplicationShowProps {
    application: Application;
    allowedTransitions: string[];
    stateHistory: StateHistoryItem[];
    canConvert: boolean;
}

function getStatusBadge(status: string) {
    const statusConfig: Record<
        string,
        {
            variant: "default" | "secondary" | "destructive" | "outline";
            className: string;
            icon: React.ReactNode;
            label: string;
        }
    > = {
        draft: {
            variant: "outline",
            className: "border-gray-400 text-gray-600",
            icon: <FileText className="h-3 w-3" />,
            label: "Draft",
        },
        in_progress: {
            variant: "outline",
            className: "border-blue-500 text-blue-600",
            icon: <Clock className="h-3 w-3" />,
            label: "In Progress",
        },
        review: {
            variant: "secondary",
            className: "bg-yellow-100 text-yellow-800",
            icon: <AlertCircle className="h-3 w-3" />,
            label: "Under Review",
        },
        approved: {
            variant: "default",
            className: "bg-green-100 text-green-800",
            icon: <CheckCircle className="h-3 w-3" />,
            label: "Approved",
        },
        rejected: {
            variant: "destructive",
            className: "bg-red-100 text-red-800",
            icon: <XCircle className="h-3 w-3" />,
            label: "Rejected",
        },
        cancelled: {
            variant: "secondary",
            className: "bg-gray-100 text-gray-800",
            icon: <XCircle className="h-3 w-3" />,
            label: "Cancelled",
        },
        on_hold: {
            variant: "secondary",
            className: "bg-orange-100 text-orange-800",
            icon: <Pause className="h-3 w-3" />,
            label: "On Hold",
        },
    };

    const config = statusConfig[status] || {
        variant: "secondary" as const,
        className: "",
        icon: null,
        label: status,
    };

    return (
        <Badge variant={config.variant} className={config.className}>
            {config.icon}
            <span className="ml-1">{config.label}</span>
        </Badge>
    );
}

function formatDate(dateString: string | null): string {
    if (!dateString) {
        return "-";
    }

    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}

function formatDateTime(dateString: string | null): string {
    if (!dateString) {
        return "-";
    }

    return new Date(dateString).toLocaleString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function formatCurrency(amount: string | number | null): string {
    if (amount === null) {
        return "-";
    }

    const num = typeof amount === "string" ? parseFloat(amount) : amount;

    return `$${num.toLocaleString()}`;
}

function getSourceLabel(source: string | null): string {
    const sources: Record<string, string> = {
        walk_in: "Walk-in",
        website: "Website",
        referral: "Referral",
        marketplace: "Marketplace",
    };

    return source ? sources[source] || source : "-";
}

export default function ApplicationShow({
    application,
    allowedTransitions,
    stateHistory,
    canConvert,
}: ApplicationShowProps) {
    const [isProcessing, setIsProcessing] = useState(false);
    const [showRejectDialog, setShowRejectDialog] = useState(false);
    const [showQuoteDialog, setShowQuoteDialog] = useState(false);

    const rejectForm = useForm({
        reason: "",
        notes: "",
    });

    const quoteForm = useForm({
        expiration_days: "30",
    });

    const handleAction = (
        action: string,
        data: Record<string, unknown> = {},
    ) => {
        setIsProcessing(true);
        router.post(
            `/lease-applications/${application.id}/${action}`,
            data as Record<string, string>,
            {
                onFinish: () => setIsProcessing(false),
            },
        );
    };

    const handleReject = () => {
        rejectForm.post(`/lease-applications/${application.id}/reject`, {
            onSuccess: () => setShowRejectDialog(false),
        });
    };

    const handleSendQuote = () => {
        quoteForm.post(`/lease-applications/${application.id}/send-quote`, {
            onSuccess: () => setShowQuoteDialog(false),
        });
    };

    const handleConvertToLease = () => {
        setIsProcessing(true);
        router.post(
            `/lease-applications/${application.id}/convert-to-lease`,
            {},
            {
                onFinish: () => setIsProcessing(false),
            },
        );
    };

    const isDraft = application.status === "draft";
    const isInProgress = application.status === "in_progress";
    const isUnderReview = application.status === "review";
    const isOnHold = application.status === "on_hold";

    return (
        <>
            <Head title={`Application ${application.application_number}`} />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link href="/lease-applications">
                            <Button variant="ghost" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back
                            </Button>
                        </Link>
                        <div>
                            <div className="flex items-center gap-3">
                                <h1 className="text-2xl font-bold tracking-tight">
                                    {application.application_number}
                                </h1>
                                {getStatusBadge(application.status)}
                            </div>
                            <p className="text-muted-foreground">
                                Created on {formatDate(application.created_at)}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center gap-2 flex-wrap">
                        {/* Workflow Actions */}
                        {isDraft &&
                            allowedTransitions.includes("in_progress") && (
                                <Button
                                    variant="default"
                                    onClick={() =>
                                        handleAction("submit-for-review")
                                    }
                                    disabled={isProcessing}
                                >
                                    <Send className="mr-2 h-4 w-4" />
                                    Submit for Review
                                </Button>
                            )}

                        {isInProgress &&
                            allowedTransitions.includes("review") && (
                                <Button
                                    variant="default"
                                    onClick={() =>
                                        handleAction("submit-for-review")
                                    }
                                    disabled={isProcessing}
                                >
                                    <Send className="mr-2 h-4 w-4" />
                                    Submit for Review
                                </Button>
                            )}

                        {isUnderReview && (
                            <>
                                <AlertDialog>
                                    <AlertDialogTrigger asChild>
                                        <Button
                                            variant="default"
                                            disabled={isProcessing}
                                        >
                                            <CheckCircle className="mr-2 h-4 w-4" />
                                            Approve
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>
                                                Approve Application
                                            </AlertDialogTitle>
                                            <AlertDialogDescription>
                                                This will approve the
                                                application. You can then
                                                convert it to a lease.
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel>
                                                Cancel
                                            </AlertDialogCancel>
                                            <AlertDialogAction
                                                onClick={() =>
                                                    handleAction("approve")
                                                }
                                            >
                                                Approve
                                            </AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>

                                <Dialog
                                    open={showRejectDialog}
                                    onOpenChange={setShowRejectDialog}
                                >
                                    <DialogTrigger asChild>
                                        <Button
                                            variant="destructive"
                                            disabled={isProcessing}
                                        >
                                            <XCircle className="mr-2 h-4 w-4" />
                                            Reject
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <DialogHeader>
                                            <DialogTitle>
                                                Reject Application
                                            </DialogTitle>
                                            <DialogDescription>
                                                Please provide a reason for
                                                rejecting this application.
                                            </DialogDescription>
                                        </DialogHeader>
                                        <div className="space-y-4">
                                            <div>
                                                <Label htmlFor="reason">
                                                    Rejection Reason *
                                                </Label>
                                                <Textarea
                                                    id="reason"
                                                    value={
                                                        rejectForm.data.reason
                                                    }
                                                    onChange={(e) =>
                                                        rejectForm.setData(
                                                            "reason",
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="Enter the reason for rejection..."
                                                />
                                            </div>
                                            <div>
                                                <Label htmlFor="notes">
                                                    Additional Notes
                                                </Label>
                                                <Textarea
                                                    id="notes"
                                                    value={
                                                        rejectForm.data.notes
                                                    }
                                                    onChange={(e) =>
                                                        rejectForm.setData(
                                                            "notes",
                                                            e.target.value,
                                                        )
                                                    }
                                                />
                                            </div>
                                        </div>
                                        <DialogFooter>
                                            <Button
                                                variant="outline"
                                                onClick={() =>
                                                    setShowRejectDialog(false)
                                                }
                                            >
                                                Cancel
                                            </Button>
                                            <Button
                                                variant="destructive"
                                                onClick={handleReject}
                                                disabled={
                                                    rejectForm.processing ||
                                                    !rejectForm.data.reason
                                                }
                                            >
                                                Reject
                                            </Button>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                            </>
                        )}

                        {isOnHold &&
                            allowedTransitions.includes("in_progress") && (
                                <Button
                                    variant="outline"
                                    onClick={() => handleAction("resume")}
                                    disabled={isProcessing}
                                >
                                    <Play className="mr-2 h-4 w-4" />
                                    Resume
                                </Button>
                            )}

                        {(isInProgress || isDraft) &&
                            allowedTransitions.includes("on_hold") && (
                                <Button
                                    variant="outline"
                                    onClick={() => handleAction("hold")}
                                    disabled={isProcessing}
                                >
                                    <Pause className="mr-2 h-4 w-4" />
                                    Put on Hold
                                </Button>
                            )}

                        {allowedTransitions.includes("cancelled") && (
                            <AlertDialog>
                                <AlertDialogTrigger asChild>
                                    <Button
                                        variant="outline"
                                        disabled={isProcessing}
                                    >
                                        <XCircle className="mr-2 h-4 w-4" />
                                        Cancel
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>
                                            Cancel Application
                                        </AlertDialogTitle>
                                        <AlertDialogDescription>
                                            Are you sure you want to cancel this
                                            application? This action cannot be
                                            undone.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>
                                            No, keep it
                                        </AlertDialogCancel>
                                        <AlertDialogAction
                                            onClick={() =>
                                                handleAction("cancel")
                                            }
                                        >
                                            Yes, cancel
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        )}

                        {/* Quote Actions */}
                        {(isInProgress || isDraft) && (
                            <Dialog
                                open={showQuoteDialog}
                                onOpenChange={setShowQuoteDialog}
                            >
                                <DialogTrigger asChild>
                                    <Button
                                        variant="outline"
                                        disabled={isProcessing}
                                    >
                                        <Mail className="mr-2 h-4 w-4" />
                                        Send Quote
                                    </Button>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>Send Quote</DialogTitle>
                                        <DialogDescription>
                                            Send a quote to the applicant via
                                            email.
                                        </DialogDescription>
                                    </DialogHeader>
                                    <div className="space-y-4">
                                        <div>
                                            <Label htmlFor="expiration_days">
                                                Quote Valid For (Days)
                                            </Label>
                                            <Input
                                                id="expiration_days"
                                                type="number"
                                                value={
                                                    quoteForm.data
                                                        .expiration_days
                                                }
                                                onChange={(e) =>
                                                    quoteForm.setData(
                                                        "expiration_days",
                                                        e.target.value,
                                                    )
                                                }
                                                min="1"
                                                max="90"
                                            />
                                        </div>
                                    </div>
                                    <DialogFooter>
                                        <Button
                                            variant="outline"
                                            onClick={() =>
                                                setShowQuoteDialog(false)
                                            }
                                        >
                                            Cancel
                                        </Button>
                                        <Button
                                            onClick={handleSendQuote}
                                            disabled={quoteForm.processing}
                                        >
                                            Send Quote
                                        </Button>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>
                        )}

                        {/* Convert to Lease */}
                        {canConvert && (
                            <AlertDialog>
                                <AlertDialogTrigger asChild>
                                    <Button
                                        variant="default"
                                        disabled={isProcessing}
                                    >
                                        <ArrowRight className="mr-2 h-4 w-4" />
                                        Convert to Lease
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>
                                            Convert to Lease
                                        </AlertDialogTitle>
                                        <AlertDialogDescription>
                                            This will create a new lease based
                                            on this application. The application
                                            will be marked as converted.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>
                                            Cancel
                                        </AlertDialogCancel>
                                        <AlertDialogAction
                                            onClick={handleConvertToLease}
                                        >
                                            Convert
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        )}

                        {/* Edit */}
                        {!application.converted_lease && (
                            <Link
                                href={`/lease-applications/${application.id}/edit`}
                            >
                                <Button variant="outline">
                                    <Edit className="mr-2 h-4 w-4" />
                                    Edit
                                </Button>
                            </Link>
                        )}
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Main Content */}
                    <div className="space-y-6 lg:col-span-2">
                        {/* Applicant Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <User className="h-5 w-5" />
                                    Applicant Information
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <p className="text-sm text-muted-foreground">
                                            Name
                                        </p>
                                        <p className="font-medium">
                                            {application.applicant?.name ||
                                                application.applicant_name}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">
                                            Type
                                        </p>
                                        <p className="font-medium capitalize">
                                            {application.applicant_type}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">
                                            Email
                                        </p>
                                        <p className="font-medium">
                                            {application.applicant?.email ||
                                                application.applicant_email}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">
                                            Phone
                                        </p>
                                        <p className="font-medium">
                                            {application.applicant_phone || "-"}
                                        </p>
                                    </div>
                                    {application.applicant_type ===
                                        "company" && (
                                        <>
                                            <div>
                                                <p className="text-sm text-muted-foreground">
                                                    Company Name
                                                </p>
                                                <p className="font-medium">
                                                    {application.company_name ||
                                                        "-"}
                                                </p>
                                            </div>
                                            <div>
                                                <p className="text-sm text-muted-foreground">
                                                    Commercial Registration
                                                </p>
                                                <p className="font-medium">
                                                    {application.commercial_registration ||
                                                        "-"}
                                                </p>
                                            </div>
                                        </>
                                    )}
                                    {application.applicant_type ===
                                        "individual" &&
                                        application.national_id && (
                                            <div>
                                                <p className="text-sm text-muted-foreground">
                                                    National ID
                                                </p>
                                                <p className="font-medium">
                                                    {application.national_id}
                                                </p>
                                            </div>
                                        )}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Lease Terms */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Calendar className="h-5 w-5" />
                                    Proposed Lease Terms
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div>
                                        <p className="text-sm text-muted-foreground">
                                            Start Date
                                        </p>
                                        <p className="font-medium">
                                            {formatDate(
                                                application.proposed_start_date,
                                            )}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">
                                            End Date
                                        </p>
                                        <p className="font-medium">
                                            {formatDate(
                                                application.proposed_end_date,
                                            )}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">
                                            Duration
                                        </p>
                                        <p className="font-medium">
                                            {application.proposed_duration_months
                                                ? `${application.proposed_duration_months} months`
                                                : "-"}
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Units */}
                        {application.units.length > 0 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Home className="h-5 w-5" />
                                        Requested Units
                                    </CardTitle>
                                    <CardDescription>
                                        {application.units.length} unit
                                        {application.units.length !== 1
                                            ? "s"
                                            : ""}{" "}
                                        requested
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Unit</TableHead>
                                                <TableHead>Building</TableHead>
                                                <TableHead>Area</TableHead>
                                                <TableHead className="text-right">
                                                    Proposed Rent
                                                </TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {application.units.map((unit) => (
                                                <TableRow key={unit.id}>
                                                    <TableCell className="font-medium">
                                                        {unit.name}
                                                    </TableCell>
                                                    <TableCell>
                                                        {unit.building?.name ||
                                                            "-"}
                                                    </TableCell>
                                                    <TableCell>
                                                        {unit.pivot?.net_area
                                                            ? `${unit.pivot.net_area} sqm`
                                                            : "-"}
                                                    </TableCell>
                                                    <TableCell className="text-right">
                                                        {formatCurrency(
                                                            unit.pivot
                                                                ?.proposed_rental_amount ||
                                                                null,
                                                        )}
                                                    </TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </CardContent>
                            </Card>
                        )}

                        {/* Special Terms */}
                        {application.special_terms && (
                            <Card>
                                <CardHeader>
                                    <CardTitle>
                                        Special Terms & Conditions
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="whitespace-pre-wrap text-sm">
                                        {application.special_terms}
                                    </p>
                                </CardContent>
                            </Card>
                        )}

                        {/* State History */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <History className="h-5 w-5" />
                                    Status History
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                {stateHistory.length === 0 ? (
                                    <p className="text-sm text-muted-foreground">
                                        No history available
                                    </p>
                                ) : (
                                    <div className="space-y-4">
                                        {stateHistory.map((item) => (
                                            <div
                                                key={item.id}
                                                className="flex gap-4 border-l-2 border-muted pl-4 pb-4"
                                            >
                                                <div className="flex-1">
                                                    <div className="flex items-center gap-2">
                                                        <span className="font-medium">
                                                            {
                                                                item.from_status_label
                                                            }{" "}
                                                            →{" "}
                                                            {
                                                                item.to_status_label
                                                            }
                                                        </span>
                                                    </div>
                                                    {item.notes && (
                                                        <p className="text-sm text-muted-foreground mt-1">
                                                            {item.notes}
                                                        </p>
                                                    )}
                                                    <p className="text-xs text-muted-foreground mt-1">
                                                        {item.changed_by &&
                                                            `By ${item.changed_by} • `}
                                                        {formatDateTime(
                                                            item.created_at,
                                                        )}
                                                    </p>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        {/* Financial Summary */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <DollarSign className="h-5 w-5" />
                                    Financial Summary
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <p className="text-sm text-muted-foreground">
                                        Quoted Rental Amount
                                    </p>
                                    <p className="text-2xl font-bold">
                                        {formatCurrency(
                                            application.quoted_rental_amount,
                                        )}
                                    </p>
                                </div>
                                {application.security_deposit && (
                                    <>
                                        <Separator />
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Security Deposit
                                            </p>
                                            <p className="text-lg font-semibold">
                                                {formatCurrency(
                                                    application.security_deposit,
                                                )}
                                            </p>
                                        </div>
                                    </>
                                )}
                            </CardContent>
                        </Card>

                        {/* Quote Status */}
                        {(application.quote_sent_at ||
                            application.quote_expires_at) && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Mail className="h-5 w-5" />
                                        Quote Status
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-2">
                                    {application.quote_sent_at && (
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Sent At
                                            </p>
                                            <p className="font-medium">
                                                {formatDateTime(
                                                    application.quote_sent_at,
                                                )}
                                            </p>
                                        </div>
                                    )}
                                    {application.quote_expires_at && (
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Expires At
                                            </p>
                                            <p className="font-medium">
                                                {formatDateTime(
                                                    application.quote_expires_at,
                                                )}
                                            </p>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>
                        )}

                        {/* Review Information */}
                        {(application.reviewed_at ||
                            application.rejection_reason) && (
                            <Card>
                                <CardHeader>
                                    <CardTitle>Review Information</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-3 text-sm">
                                    {application.reviewed_by && (
                                        <div>
                                            <p className="text-muted-foreground">
                                                Reviewed By
                                            </p>
                                            <p className="font-medium">
                                                {application.reviewed_by.name}
                                            </p>
                                        </div>
                                    )}
                                    {application.reviewed_at && (
                                        <div>
                                            <p className="text-muted-foreground">
                                                Reviewed At
                                            </p>
                                            <p className="font-medium">
                                                {formatDateTime(
                                                    application.reviewed_at,
                                                )}
                                            </p>
                                        </div>
                                    )}
                                    {application.review_notes && (
                                        <div>
                                            <p className="text-muted-foreground">
                                                Review Notes
                                            </p>
                                            <p className="font-medium">
                                                {application.review_notes}
                                            </p>
                                        </div>
                                    )}
                                    {application.rejection_reason && (
                                        <div>
                                            <p className="text-muted-foreground">
                                                Rejection Reason
                                            </p>
                                            <p className="font-medium text-red-600">
                                                {application.rejection_reason}
                                            </p>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>
                        )}

                        {/* Converted Lease */}
                        {application.converted_lease && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <FileText className="h-5 w-5" />
                                        Converted Lease
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="font-medium">
                                        {application.converted_lease
                                            .contract_number ||
                                            `#${application.converted_lease.id}`}
                                    </p>
                                    <p className="text-sm text-muted-foreground">
                                        Converted on{" "}
                                        {formatDate(application.converted_at)}
                                    </p>
                                    <Link
                                        href={`/leases/${application.converted_lease.id}`}
                                    >
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            className="mt-2"
                                        >
                                            View Lease
                                        </Button>
                                    </Link>
                                </CardContent>
                            </Card>
                        )}

                        {/* Property Info */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Building2 className="h-5 w-5" />
                                    Property
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-2">
                                {application.community && (
                                    <div>
                                        <p className="text-sm text-muted-foreground">
                                            Community
                                        </p>
                                        <p className="font-medium">
                                            {application.community.name}
                                        </p>
                                    </div>
                                )}
                                {application.building && (
                                    <div>
                                        <p className="text-sm text-muted-foreground">
                                            Building
                                        </p>
                                        <p className="font-medium">
                                            {application.building.name}
                                        </p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        {/* Additional Info */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Additional Info</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3 text-sm">
                                <div>
                                    <p className="text-muted-foreground">
                                        Source
                                    </p>
                                    <p className="font-medium">
                                        {getSourceLabel(application.source)}
                                    </p>
                                </div>
                                {application.assigned_to && (
                                    <div>
                                        <p className="text-muted-foreground">
                                            Assigned To
                                        </p>
                                        <p className="font-medium">
                                            {application.assigned_to.name}
                                        </p>
                                    </div>
                                )}
                                {application.created_by && (
                                    <div>
                                        <p className="text-muted-foreground">
                                            Created By
                                        </p>
                                        <p className="font-medium">
                                            {application.created_by.name}
                                        </p>
                                    </div>
                                )}
                                <div>
                                    <p className="text-muted-foreground">
                                        Last Updated
                                    </p>
                                    <p className="font-medium">
                                        {formatDate(application.updated_at)}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Internal Notes */}
                        {application.notes && (
                            <Card>
                                <CardHeader>
                                    <CardTitle>Internal Notes</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="whitespace-pre-wrap text-sm">
                                        {application.notes}
                                    </p>
                                </CardContent>
                            </Card>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}

ApplicationShow.layout = {
    breadcrumbs: [
        { title: "Lease Applications", href: "/lease-applications" },
        { title: "Details", href: "" },
    ],
};
