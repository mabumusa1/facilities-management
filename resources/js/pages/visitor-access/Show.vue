<script setup lang="ts">
import { watch } from 'vue';
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

const { t } = useI18n();

interface InvitationDetail {
    id: number;
    visitor_name: string;
    visitor_phone: string | null;
    visitor_purpose: string;
    expected_at: string | null;
    valid_until: string | null;
    status: string;
    qr_code_token: string;
    qr_svg: string;
}

const props = defineProps<{
    invitation: InvitationDetail;
}>();

watch(
    () => t('app.visitorAccess.show.pageTitle'),
    () => {
        setLayoutProps({
            breadcrumbs: [
                { title: t('app.navigation.dashboard'), href: '/dashboard' },
                { title: t('app.visitorAccess.myVisitors.pageTitle'), href: '/visitor-access/invitations' },
                { title: t('app.visitorAccess.show.pageTitle'), href: `/visitor-access/invitations/${props.invitation.id}` },
            ],
        });
    },
    { immediate: true },
);

function purposeLabel(purpose: string): string {
    const map: Record<string, string> = {
        visit: t('app.visitorAccess.myVisitors.purposeVisit'),
        delivery: t('app.visitorAccess.myVisitors.purposeDelivery'),
        service: t('app.visitorAccess.myVisitors.purposeService'),
        other: t('app.visitorAccess.myVisitors.purposeOther'),
    };

    return map[purpose] ?? purpose;
}

function formatDateTime(iso: string | null): string {
    if (! iso) {
        return t('app.common.notAvailable');
    }

    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(iso));
}

async function shareQr() {
    const shareText = `${t('app.visitorAccess.show.shareText')} — ${props.invitation.qr_code_token}`;

    if (navigator.share) {
        try {
            await navigator.share({
                title: t('app.visitorAccess.show.pageTitle'),
                text: shareText,
            });
        } catch {
            // User dismissed the share sheet — no action needed.
        }

        return;
    }

    // Fallback: copy token to clipboard.
    await navigator.clipboard.writeText(shareText);
}
</script>

<template>
    <Head :title="t('app.visitorAccess.show.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.visitorAccess.show.heading')"
            :description="t('app.visitorAccess.show.description')"
        />

        <Card class="max-w-2xl">
            <CardContent class="flex flex-col items-center gap-6 p-6">
                <!-- Invitation summary -->
                <div class="flex w-full flex-col gap-1 text-sm">
                    <p class="text-lg font-semibold">{{ props.invitation.visitor_name }}</p>
                    <p class="text-muted-foreground">
                        {{ purposeLabel(props.invitation.visitor_purpose) }} ·
                        {{ formatDateTime(props.invitation.expected_at) }}
                    </p>
                    <Badge variant="default" class="w-fit">
                        {{ t('app.visitorAccess.myVisitors.statusActive') }}
                    </Badge>
                </div>

                <!-- QR code image -->
                <img
                    :src="props.invitation.qr_svg"
                    :alt="t('app.visitorAccess.show.qrAlt', { name: props.invitation.visitor_name, until: formatDateTime(props.invitation.valid_until) })"
                    class="w-full max-w-xs rounded-lg border p-2"
                    aria-live="polite"
                />

                <!-- Valid until -->
                <p class="text-muted-foreground text-sm">
                    {{ t('app.visitorAccess.show.validUntil') }}: {{ formatDateTime(props.invitation.valid_until) }}
                </p>

                <!-- Share hint -->
                <div class="bg-muted rounded-md p-4 text-sm">
                    <p>{{ t('app.visitorAccess.show.shareHint') }}</p>
                </div>

                <!-- Action buttons -->
                <div class="flex w-full flex-col gap-3 sm:flex-row">
                    <Button class="flex-1" :aria-label="t('app.visitorAccess.show.shareCta')" @click="shareQr">
                        {{ t('app.visitorAccess.show.shareCta') }}
                    </Button>
                    <Button variant="outline" class="flex-1" as-child>
                        <Link href="/visitor-access/invitations">
                            {{ t('app.visitorAccess.show.viewMyVisitorsCta') }}
                        </Link>
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
