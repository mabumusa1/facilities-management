<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { reactive, watchEffect } from 'vue';
import { update as alertsUpdate } from '@/actions/App/Http/Controllers/Leasing/LeaseAlertSettingsController';
import { index as pipelineIndex } from '@/actions/App/Http/Controllers/Leasing/LeasePipelineController';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

type Threshold = {
    days: number;
    in_app: boolean;
    email: boolean;
};

const props = defineProps<{
    thresholds: Threshold[];
    defaultThresholds: Threshold[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            { title: t('app.pipeline.title'), href: pipelineIndex.url() },
            { title: t('app.pipeline.settings.title'), href: '#' },
        ],
    });
});

const form = reactive({
    thresholds: props.thresholds.map((t) => ({ ...t })),
});

function addThreshold() {
    form.thresholds.push({ days: 14, in_app: true, email: false });
}

function removeThreshold(index: number) {
    form.thresholds.splice(index, 1);
}

function resetToDefaults() {
    form.thresholds = props.defaultThresholds.map((t) => ({ ...t }));
}
</script>

<template>
    <div>
        <Head :title="t('app.pipeline.settings.title')" />

        <PageHeader :title="t('app.pipeline.settings.title')">
            <template #actions>
                <Button variant="ghost" as-child size="sm">
                    <Link :href="pipelineIndex.url()">
                        {{ t('app.pipeline.settings.backToPipeline') }}
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <div class="p-4 max-w-2xl">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.pipeline.settings.title') }}</CardTitle>
                    <p class="text-sm text-muted-foreground">{{ t('app.pipeline.settings.description') }}</p>
                </CardHeader>
                <CardContent>
                    <Form v-bind="alertsUpdate.form()" #default="{ processing }">
                        <div class="space-y-6">
                            <h3 class="font-medium">{{ t('app.pipeline.settings.alertThresholds') }}</h3>

                            <div
                                v-for="(threshold, index) in form.thresholds"
                                :key="index"
                                class="space-y-3 rounded-md border p-4"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <Label :for="`days-${index}`" class="w-32">
                                            {{ t('app.pipeline.settings.customDays') }}
                                        </Label>
                                        <Input
                                            :id="`days-${index}`"
                                            :name="`thresholds[${index}][days]`"
                                            type="number"
                                            min="1"
                                            max="365"
                                            :value="threshold.days"
                                            class="w-24"
                                            @input="threshold.days = Number(($event.target as HTMLInputElement).value)"
                                        />
                                    </div>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        @click="removeThreshold(index)"
                                    >
                                        {{ t('app.pipeline.settings.removeThreshold') }}
                                    </Button>
                                </div>

                                <div class="flex gap-6">
                                    <div class="flex items-center gap-2">
                                        <Checkbox
                                            :id="`in-app-${index}`"
                                            :name="`thresholds[${index}][in_app]`"
                                            :checked="threshold.in_app"
                                            :aria-label="`${t('app.pipeline.settings.inApp')} — ${threshold.days} ${t('app.pipeline.settings.customDays')}`"
                                            @update:checked="threshold.in_app = $event"
                                        />
                                        <input
                                            type="hidden"
                                            :name="`thresholds[${index}][in_app]`"
                                            :value="threshold.in_app ? '1' : '0'"
                                        />
                                        <Label :for="`in-app-${index}`">{{ t('app.pipeline.settings.inApp') }}</Label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <Checkbox
                                            :id="`email-${index}`"
                                            :name="`thresholds[${index}][email]`"
                                            :checked="threshold.email"
                                            :aria-label="`${t('app.pipeline.settings.email')} — ${threshold.days} ${t('app.pipeline.settings.customDays')}`"
                                            @update:checked="threshold.email = $event"
                                        />
                                        <input
                                            type="hidden"
                                            :name="`thresholds[${index}][email]`"
                                            :value="threshold.email ? '1' : '0'"
                                        />
                                        <Label :for="`email-${index}`">{{ t('app.pipeline.settings.email') }}</Label>
                                    </div>
                                </div>
                            </div>

                            <Button type="button" variant="outline" size="sm" @click="addThreshold">
                                {{ t('app.pipeline.settings.addCustom') }}
                            </Button>

                            <Separator />

                            <div class="flex items-center justify-between">
                                <Button type="button" variant="ghost" @click="resetToDefaults">
                                    {{ t('app.pipeline.settings.resetDefaults') }}
                                </Button>
                                <Button type="submit" :disabled="processing">
                                    {{ processing ? '…' : t('app.pipeline.settings.save') }}
                                </Button>
                            </div>
                        </div>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
