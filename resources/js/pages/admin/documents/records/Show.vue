<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import Heading from '@/components/Heading.vue';
import { send, resend } from '@/routes/admin/documents/records';
import { ref } from 'vue';

const { t } = useI18n();

defineProps<{
    record: {
        id: number;
        status: string;
        source_type: string;
        source_id: number;
        generated_at: string | null;
        signing_token: string | null;
        template_name: { en: string; ar: string | null } | null;
        version_number: number | null;
        signatures: { id: number; signer_name: string; signer_email: string; signed_at: string | null }[];
    };
}>();

const sendForm = useForm({
    signer_name: '',
    signer_email: '',
    signer_phone: '',
});

const showSendForm = ref(false);

function statusVariant(status: string): 'default' | 'success' | 'secondary' {
    return status === 'signed' ? 'success' : status === 'sent' ? 'secondary' : 'default';
}

function submitSend() {
    sendForm.post(send.url({ documentRecord: props.record.id }), {
        preserveScroll: true,
        onSuccess: () => { showSendForm.value = false; },
    });
}
</script>

<template>
    <Head :title="`Document Record #${record.id}`" />
    <Heading title="Document Record" :description="`#${record.id}`" />

    <div class="space-y-6 max-w-2xl">
        <div class="border rounded-lg p-4 space-y-2">
            <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Status</span>
                <Badge :variant="statusVariant(record.status)">{{ record.status }}</Badge>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Template</span>
                <span>{{ record.template_name?.en ?? '—' }} v{{ record.version_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Source</span>
                <span>{{ record.source_type }} #{{ record.source_id }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Generated</span>
                <span>{{ record.generated_at ? new Date(record.generated_at).toLocaleString() : '—' }}</span>
            </div>
        </div>

        <div v-if="record.signatures.length" class="border rounded-lg p-4">
            <h3 class="font-semibold mb-2">Signatures</h3>
            <div v-for="sig in record.signatures" :key="sig.id" class="text-sm space-y-1">
                <div><strong>{{ sig.signer_name }}</strong> ({{ sig.signer_email }})</div>
                <div class="text-muted-foreground">{{ sig.signed_at ? 'Signed ' + new Date(sig.signed_at).toLocaleString() : 'Not yet signed' }}</div>
            </div>
        </div>

        <div v-if="record.status === 'draft' || record.status === 'link_expired'" class="border rounded-lg p-4">
            <Button v-if="!showSendForm" @click="showSendForm = true">Send for Signature</Button>

            <div v-if="showSendForm" class="space-y-4 mt-4">
                <div class="grid gap-2">
                    <Label for="signer_name">Recipient Name</Label>
                    <Input id="signer_name" v-model="sendForm.signer_name" required />
                    <InputError :message="sendForm.errors.signer_name" />
                </div>
                <div class="grid gap-2">
                    <Label for="signer_email">Recipient Email</Label>
                    <Input id="signer_email" v-model="sendForm.signer_email" type="email" required />
                    <InputError :message="sendForm.errors.signer_email" />
                </div>
                <div class="grid gap-2">
                    <Label for="signer_phone">Recipient Phone (optional)</Label>
                    <Input id="signer_phone" v-model="sendForm.signer_phone" />
                    <InputError :message="sendForm.errors.signer_phone" />
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" @click="showSendForm = false">Cancel</Button>
                    <Button @click="submitSend" :disabled="sendForm.processing">Send</Button>
                </div>
            </div>
        </div>

        <div v-if="record.status === 'sent'" class="border rounded-lg p-4">
            <div v-if="record.signing_token" class="mb-2 text-sm text-muted-foreground break-all">
                Signing link: <code>/sign/{{ record.signing_token }}</code>
            </div>
            <Button variant="outline" @click="$inertia.post(resend.url({ documentRecord: record.id }))">
                Resend Link
            </Button>
        </div>
    </div>
</template>

<script lang="ts">
export default { props: undefined };
</script>
