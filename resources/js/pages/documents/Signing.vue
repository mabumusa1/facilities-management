<script setup lang="ts">
import { Head, useForm, useHttp } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { sign, requestOtp } from '@/routes/signing';
import { CheckCircle, AlertCircle } from 'lucide-vue-next';

const props = defineProps<{
    record: {
        id: number;
        signing_token: string;
        status: string;
        body: { en: string; ar: string } | null;
        signer_name: string;
    };
}>();

const lang = ref<'en' | 'ar'>('en');
const body = computed(() => props.record.body?.[lang.value] ?? props.record.body?.en ?? '');

const step = ref<'review' | 'otp' | 'signed'>('review');
const otpSent = ref(false);
const otpError = ref('');
const signerName = ref(props.record.signer_name || '');

const otpForm = useForm({ otp: '' });

async function onRequestOtp() {
    otpError.value = '';
    try {
        const res = await useHttp().post(requestOtp.url({ token: props.record.signing_token }));
        otpSent.value = true;
    } catch (e: any) {
        otpError.value = e?.response?.data?.error || 'Failed to send OTP';
    }
}

async function onSign() {
    otpError.value = '';
    try {
        const res = await useHttp().post(sign.url({ token: props.record.signing_token }), {
            otp: otpForm.otp,
            signer_name: signerName.value || props.record.signer_name,
        });
        step.value = 'signed';
    } catch (e: any) {
        otpError.value = e?.response?.data?.error || 'Invalid OTP';
    }
}
</script>

<template>
    <Head title="Sign Document" />

    <div class="min-h-screen bg-muted/30 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl bg-background border rounded-lg p-8 shadow-sm">
            <!-- Review Step -->
            <div v-if="step === 'review'">
                <h1 class="text-xl font-semibold mb-2">Review &amp; Sign</h1>
                <p class="text-muted-foreground text-sm mb-4">
                    Please review this document before signing. You are signing as:
                    <strong>{{ record.signer_name }}</strong>
                </p>

                <div class="flex gap-2 mb-4">
                    <Button variant="outline" size="sm" :class="lang === 'en' ? 'bg-primary/10' : ''" @click="lang = 'en'">English</Button>
                    <Button variant="outline" size="sm" :class="lang === 'ar' ? 'bg-primary/10' : ''" @click="lang = 'ar'">العربية</Button>
                </div>

                <div class="border rounded-lg p-6 bg-muted/20 min-h-[300px] text-sm whitespace-pre-wrap font-mono"
                    :dir="lang === 'ar' ? 'rtl' : 'ltr'">
                    {{ body || '(empty document)' }}
                </div>

                <Button class="mt-6 w-full" size="lg" @click="step = 'otp'">
                    Sign Document
                </Button>
            </div>

            <!-- OTP Step -->
            <div v-if="step === 'otp'">
                <h1 class="text-xl font-semibold mb-2">Verify Your Identity</h1>
                <p class="text-muted-foreground text-sm mb-6">
                    An OTP will be sent to your contact. Enter the 6-digit code to complete signing.
                </p>

                <div v-if="otpError" class="border border-red-200 bg-red-50 rounded-lg p-3 flex gap-2 text-sm text-red-800 mb-4">
                    <AlertCircle class="h-4 w-4 mt-0.5 shrink-0" />
                    {{ otpError }}
                </div>

                <div class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="signer_name">Your Name</Label>
                        <Input id="signer_name" v-model="signerName" required />
                    </div>

                    <div class="grid gap-2" v-if="otpSent">
                        <Label for="otp">6-Digit OTP</Label>
                        <Input id="otp" v-model="otpForm.otp" maxlength="6" placeholder="000000" class="text-center text-2xl tracking-widest" />
                        <InputError :message="otpForm.errors.otp" />
                    </div>

                    <Button v-if="!otpSent" class="w-full" @click="onRequestOtp">Send OTP</Button>
                    <Button v-else class="w-full" @click="onSign">Verify &amp; Sign</Button>
                    <Button variant="ghost" class="w-full" @click="step = 'review'">← Back to review</Button>
                </div>
            </div>

            <!-- Signed Step -->
            <div v-if="step === 'signed'" class="text-center py-12">
                <CheckCircle class="h-12 w-12 text-green-500 mx-auto mb-4" />
                <h1 class="text-xl font-semibold mb-2">Document Signed</h1>
                <p class="text-muted-foreground text-sm">
                    Your signature has been recorded. You may close this page.
                </p>
            </div>
        </div>
    </div>
</template>
