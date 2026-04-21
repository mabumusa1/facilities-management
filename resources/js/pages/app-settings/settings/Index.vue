<script setup lang="ts">
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { edit as editRequestCategory } from '@/actions/App/Http/Controllers/AppSettings/RequestCategoryController';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { details as serviceRequestDetails } from '@/routes/settings/service-request';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';

const { t, isArabic } = useI18n();

type SettingsTab = {
    key: string;
    label: string;
    href: string;
};

type InvoiceSetting = {
    id: number;
    company_name: string;
    logo: string | null;
    address: string | null;
    vat: string;
    vat_number: string | null;
    cr_number: string | null;
    instructions: string | null;
    notes: string | null;
};

type ServiceRequestType = {
    key: string;
    label: string;
    description: string;
};

type ServiceRequestSubcategory = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    status: boolean;
};

type ServiceRequestCategory = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    status: boolean;
    has_sub_categories: boolean;
    subcategories: ServiceRequestSubcategory[];
};

type ServiceRequestSettings = {
    types: ServiceRequestType[];
    categories: ServiceRequestCategory[];
};

type VisitorRequestSetting = {
    enabled?: boolean;
    require_pre_approval?: boolean;
    max_visitors_per_request?: number;
    allowed_visit_duration_minutes?: number | null;
    notes?: string | null;
};

type BankDetailsSetting = {
    beneficiary_name?: string;
    bank_name?: string;
    account_number?: string;
    iban?: string;
};

type VisitsDetailsSetting = {
    is_all_day?: boolean;
    days?: string[];
    start_time?: string | null;
    end_time?: string | null;
    max_daily_visits?: number | null;
};

type SalesDetailsSetting = {
    deposit_time_limit_days?: number;
    cash_contract_signing_days?: number;
    bank_contract_signing_days?: number;
};

const props = defineProps<{
    activeTab: string;
    pageTitle: string;
    tabs: SettingsTab[];
    invoiceSetting?: InvoiceSetting | null;
    serviceRequestSettings?: ServiceRequestSettings;
    visitorRequestSetting?: VisitorRequestSetting | null;
    bankDetailsSetting?: BankDetailsSetting | null;
    visitsDetailsSetting?: VisitsDetailsSetting | null;
    salesDetailsSetting?: SalesDetailsSetting | null;
}>();

const tabTranslationKeys: Record<string, string> = {
    invoice: 'app.appSettings.shell.tabs.invoice',
    'service-request': 'app.appSettings.shell.tabs.serviceRequest',
    'visitor-request': 'app.appSettings.shell.tabs.visitorRequest',
    'bank-details': 'app.appSettings.shell.tabs.bankDetails',
    'visits-details': 'app.appSettings.shell.tabs.visitsDetails',
    'sales-details': 'app.appSettings.shell.tabs.salesDetails',
};

const serviceRequestTypeTranslationKeys: Record<string, string> = {
    'home-service': 'app.appSettings.shell.requestTypes.homeService',
    'neighbourhood-service': 'app.appSettings.shell.requestTypes.neighbourhoodService',
};

const resolvedPageTitle = computed(() => {
    const key = tabTranslationKeys[props.activeTab];

    return key ? t(key) : props.pageTitle;
});

function tabLabel(key: string): string {
    const translationKey = tabTranslationKeys[key];

    return translationKey ? t(translationKey) : key;
}

function serviceRequestTypeLabel(key: string): string {
    const translationKey = serviceRequestTypeTranslationKeys[key];

    return translationKey ? t(translationKey) : key;
}

function localizedCategoryName(category: ServiceRequestCategory): string {
    if (isArabic.value) {
        return category.name_ar ?? category.name ?? category.name_en;
    }

    return category.name_en ?? category.name ?? category.name_ar;
}

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.settings'), href: '/settings/invoice' },
        ],
    });
});

const invoiceForm = useForm({
    company_name: props.invoiceSetting?.company_name ?? '',
    address: props.invoiceSetting?.address ?? '',
    vat: props.invoiceSetting?.vat ?? '',
    vat_number: props.invoiceSetting?.vat_number ?? '',
    cr_number: props.invoiceSetting?.cr_number ?? '',
    instructions: props.invoiceSetting?.instructions ?? '',
    notes: props.invoiceSetting?.notes ?? '',
});

function saveInvoiceSettings() {
    invoiceForm.post('/settings/invoice', {
        preserveScroll: true,
    });
}

const visitorRequestForm = useForm({
    enabled: props.visitorRequestSetting?.enabled ?? true,
    require_pre_approval: props.visitorRequestSetting?.require_pre_approval ?? false,
    max_visitors_per_request: props.visitorRequestSetting?.max_visitors_per_request ?? 1,
    allowed_visit_duration_minutes: props.visitorRequestSetting?.allowed_visit_duration_minutes ?? null as number | null,
    notes: props.visitorRequestSetting?.notes ?? '',
});

const bankDetailsForm = useForm({
    beneficiary_name: props.bankDetailsSetting?.beneficiary_name ?? '',
    bank_name: props.bankDetailsSetting?.bank_name ?? '',
    account_number: props.bankDetailsSetting?.account_number ?? '',
    iban: props.bankDetailsSetting?.iban ?? '',
});

const visitsDetailsForm = useForm({
    is_all_day: props.visitsDetailsSetting?.is_all_day ?? true,
    days: props.visitsDetailsSetting?.days ?? ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
    start_time: props.visitsDetailsSetting?.start_time ?? '',
    end_time: props.visitsDetailsSetting?.end_time ?? '',
    max_daily_visits: props.visitsDetailsSetting?.max_daily_visits ?? null as number | null,
});

const salesDetailsForm = useForm({
    deposit_time_limit_days: props.salesDetailsSetting?.deposit_time_limit_days ?? 7,
    cash_contract_signing_days: props.salesDetailsSetting?.cash_contract_signing_days ?? 14,
    bank_contract_signing_days: props.salesDetailsSetting?.bank_contract_signing_days ?? 30,
});

function saveVisitorRequestSettings() {
    visitorRequestForm.post('/settings/visitor-request', {
        preserveScroll: true,
    });
}

function saveBankDetailsSettings() {
    bankDetailsForm.post('/settings/bank-details', {
        preserveScroll: true,
    });
}

function saveVisitsDetailsSettings() {
    visitsDetailsForm.post('/settings/visits-details', {
        preserveScroll: true,
    });
}

function saveSalesDetailsSettings() {
    salesDetailsForm.post('/settings/sales-details', {
        preserveScroll: true,
    });
}

function categoryCode(value: string): string {
    return value
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '') || 'category';
}
</script>

<template>
    <Head :title="t('app.appSettings.shell.pageTitle', { title: resolvedPageTitle })" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.appSettings.shell.heading', { title: resolvedPageTitle })"
            :description="t('app.appSettings.shell.description')"
        />

        <div class="flex flex-wrap gap-2">
            <Button
                v-for="tab in props.tabs"
                :key="tab.key"
                :variant="tab.key === props.activeTab ? 'default' : 'outline'"
                as-child
            >
                <Link :href="tab.href">{{ tabLabel(tab.key) }}</Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ resolvedPageTitle }}</CardTitle>
                <CardDescription>
                    {{ t('app.appSettings.shell.cardDescription') }}
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div v-if="props.activeTab === 'invoice'" class="space-y-3 text-sm">
                    <form @submit.prevent="saveInvoiceSettings" class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="company_name">{{ t('app.appSettings.shell.companyName') }}</Label>
                            <Input id="company_name" v-model="invoiceForm.company_name" required :placeholder="t('app.appSettings.shell.companyNamePlaceholder')" />
                            <InputError :message="invoiceForm.errors.company_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="address">{{ t('app.appSettings.shell.address') }}</Label>
                            <Textarea id="address" v-model="invoiceForm.address" required :placeholder="t('app.appSettings.shell.addressPlaceholder')" />
                            <InputError :message="invoiceForm.errors.address" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="grid gap-2">
                                <Label for="vat">{{ t('app.appSettings.shell.vat') }}</Label>
                                <Input id="vat" v-model="invoiceForm.vat" type="number" step="0.01" min="0" max="100" required placeholder="15.00" />
                                <InputError :message="invoiceForm.errors.vat" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="vat_number">{{ t('app.appSettings.shell.vatNumber') }}</Label>
                                <Input id="vat_number" v-model="invoiceForm.vat_number" :placeholder="t('app.appSettings.shell.vatNumberPlaceholder')" />
                                <InputError :message="invoiceForm.errors.vat_number" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="cr_number">{{ t('app.appSettings.shell.crNumber') }}</Label>
                                <Input id="cr_number" v-model="invoiceForm.cr_number" :placeholder="t('app.appSettings.shell.crNumberPlaceholder')" />
                                <InputError :message="invoiceForm.errors.cr_number" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="instructions">{{ t('app.appSettings.shell.paymentInstructions') }}</Label>
                            <Textarea id="instructions" v-model="invoiceForm.instructions" :placeholder="t('app.appSettings.shell.paymentInstructionsPlaceholder')" />
                            <InputError :message="invoiceForm.errors.instructions" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="notes">{{ t('app.appSettings.shell.notes') }}</Label>
                            <Textarea id="notes" v-model="invoiceForm.notes" :placeholder="t('app.appSettings.shell.invoiceNotesPlaceholder')" />
                            <InputError :message="invoiceForm.errors.notes" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button :disabled="invoiceForm.processing">{{ t('app.appSettings.shell.saveInvoiceSettings') }}</Button>
                        </div>
                    </form>
                </div>
                <div v-else-if="props.activeTab === 'service-request'" class="space-y-6 text-sm">
                    <div class="space-y-2">
                        <p class="text-sm font-medium">{{ t('app.appSettings.shell.serviceRequestTypes') }}</p>
                        <div class="flex flex-wrap gap-2">
                            <Badge v-for="type in props.serviceRequestSettings?.types ?? []" :key="type.key" variant="secondary">
                                {{ serviceRequestTypeLabel(type.key) }}
                            </Badge>
                        </div>
                    </div>

                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>{{ t('app.appSettings.shell.category') }}</TableHead>
                                <TableHead>{{ t('app.appSettings.shell.status') }}</TableHead>
                                <TableHead>{{ t('app.appSettings.shell.subcategories') }}</TableHead>
                                <TableHead class="text-right">{{ t('app.appSettings.shell.navigation') }}</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="category in props.serviceRequestSettings?.categories ?? []" :key="category.id">
                                <TableCell>{{ localizedCategoryName(category) }}</TableCell>
                                <TableCell>
                                    <Badge :variant="category.status ? 'default' : 'secondary'">
                                        {{ category.status ? t('app.common.active') : t('app.common.inactive') }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ category.subcategories.length }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button size="sm" variant="outline" as-child>
                                            <Link :href="serviceRequestDetails({ type: 'home-service', catCode: categoryCode(category.name_en ?? category.name), catId: category.id }).url">{{ t('app.appSettings.shell.details') }}</Link>
                                        </Button>
                                        <Button size="sm" variant="outline" as-child>
                                            <Link :href="editRequestCategory(category.id).url">{{ t('app.appSettings.shell.openCategory') }}</Link>
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="(props.serviceRequestSettings?.categories ?? []).length === 0">
                                <TableCell :colspan="4" class="text-muted-foreground text-center">
                                    {{ t('app.appSettings.shell.noServiceRequestCategories') }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
                <div v-else-if="props.activeTab === 'visitor-request'" class="space-y-3 text-sm">
                    <form @submit.prevent="saveVisitorRequestSettings" class="space-y-4">
                        <div class="flex flex-wrap gap-6">
                            <label class="flex items-center gap-2 text-sm">
                                <input v-model="visitorRequestForm.enabled" type="checkbox" />
                                {{ t('app.appSettings.shell.enabled') }}
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input v-model="visitorRequestForm.require_pre_approval" type="checkbox" />
                                {{ t('app.appSettings.shell.requirePreApproval') }}
                            </label>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="max_visitors_per_request">{{ t('app.appSettings.shell.maxVisitorsPerRequest') }}</Label>
                                <Input id="max_visitors_per_request" v-model.number="visitorRequestForm.max_visitors_per_request" type="number" min="1" max="50" />
                                <InputError :message="visitorRequestForm.errors.max_visitors_per_request" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="allowed_visit_duration_minutes">{{ t('app.appSettings.shell.allowedVisitDurationMinutes') }}</Label>
                                <Input id="allowed_visit_duration_minutes" v-model.number="visitorRequestForm.allowed_visit_duration_minutes" type="number" min="15" max="1440" />
                                <InputError :message="visitorRequestForm.errors.allowed_visit_duration_minutes" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="visitor_notes">{{ t('app.appSettings.shell.notes') }}</Label>
                            <Textarea id="visitor_notes" v-model="visitorRequestForm.notes" rows="3" />
                            <InputError :message="visitorRequestForm.errors.notes" />
                        </div>

                        <Button :disabled="visitorRequestForm.processing">{{ t('app.appSettings.shell.saveVisitorRequestSettings') }}</Button>
                    </form>
                </div>

                <div v-else-if="props.activeTab === 'bank-details'" class="space-y-3 text-sm">
                    <form @submit.prevent="saveBankDetailsSettings" class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="beneficiary_name">{{ t('app.appSettings.shell.beneficiaryName') }}</Label>
                            <Input id="beneficiary_name" v-model="bankDetailsForm.beneficiary_name" />
                            <InputError :message="bankDetailsForm.errors.beneficiary_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="bank_name">{{ t('app.appSettings.shell.bankName') }}</Label>
                            <Input id="bank_name" v-model="bankDetailsForm.bank_name" />
                            <InputError :message="bankDetailsForm.errors.bank_name" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="account_number">{{ t('app.appSettings.shell.accountNumber') }}</Label>
                                <Input id="account_number" v-model="bankDetailsForm.account_number" />
                                <InputError :message="bankDetailsForm.errors.account_number" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="iban">{{ t('app.appSettings.shell.iban') }}</Label>
                                <Input id="iban" v-model="bankDetailsForm.iban" />
                                <InputError :message="bankDetailsForm.errors.iban" />
                            </div>
                        </div>

                        <Button :disabled="bankDetailsForm.processing">{{ t('app.appSettings.shell.saveBankDetails') }}</Button>
                    </form>
                </div>

                <div v-else-if="props.activeTab === 'visits-details'" class="space-y-3 text-sm">
                    <form @submit.prevent="saveVisitsDetailsSettings" class="space-y-4">
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="visitsDetailsForm.is_all_day" type="checkbox" />
                            {{ t('app.appSettings.shell.allDayVisits') }}
                        </label>

                        <div class="grid gap-2">
                            <Label for="days">{{ t('app.appSettings.shell.availableDays') }}</Label>
                            <Input
                                id="days"
                                :model-value="visitsDetailsForm.days.join(', ')"
                                @update:model-value="(value) => { visitsDetailsForm.days = String(value).split(',').map((item) => item.trim()).filter(Boolean); }"
                            />
                            <InputError :message="visitsDetailsForm.errors.days" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="grid gap-2">
                                <Label for="start_time">{{ t('app.appSettings.shell.startTime') }}</Label>
                                <Input id="start_time" v-model="visitsDetailsForm.start_time" type="time" />
                                <InputError :message="visitsDetailsForm.errors.start_time" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="end_time">{{ t('app.appSettings.shell.endTime') }}</Label>
                                <Input id="end_time" v-model="visitsDetailsForm.end_time" type="time" />
                                <InputError :message="visitsDetailsForm.errors.end_time" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="max_daily_visits">{{ t('app.appSettings.shell.maxDailyVisits') }}</Label>
                                <Input id="max_daily_visits" v-model.number="visitsDetailsForm.max_daily_visits" type="number" min="1" max="1000" />
                                <InputError :message="visitsDetailsForm.errors.max_daily_visits" />
                            </div>
                        </div>

                        <Button :disabled="visitsDetailsForm.processing">{{ t('app.appSettings.shell.saveVisitsDetails') }}</Button>
                    </form>
                </div>

                <div v-else-if="props.activeTab === 'sales-details'" class="space-y-3 text-sm">
                    <form @submit.prevent="saveSalesDetailsSettings" class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="grid gap-2">
                                <Label for="deposit_time_limit_days">{{ t('app.appSettings.shell.depositTimeLimitDays') }}</Label>
                                <Input id="deposit_time_limit_days" v-model.number="salesDetailsForm.deposit_time_limit_days" type="number" min="1" max="365" />
                                <InputError :message="salesDetailsForm.errors.deposit_time_limit_days" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="cash_contract_signing_days">{{ t('app.appSettings.shell.cashContractSigningDays') }}</Label>
                                <Input id="cash_contract_signing_days" v-model.number="salesDetailsForm.cash_contract_signing_days" type="number" min="1" max="365" />
                                <InputError :message="salesDetailsForm.errors.cash_contract_signing_days" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="bank_contract_signing_days">{{ t('app.appSettings.shell.bankContractSigningDays') }}</Label>
                                <Input id="bank_contract_signing_days" v-model.number="salesDetailsForm.bank_contract_signing_days" type="number" min="1" max="365" />
                                <InputError :message="salesDetailsForm.errors.bank_contract_signing_days" />
                            </div>
                        </div>

                        <Button :disabled="salesDetailsForm.processing">{{ t('app.appSettings.shell.saveSalesDetails') }}</Button>
                    </form>
                </div>

                <p v-else class="text-muted-foreground text-sm">{{ t('app.appSettings.shell.unsupportedSettingsTab') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
