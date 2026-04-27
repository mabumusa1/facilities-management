<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Spinner } from '@/components/ui/spinner';
import { useI18n } from '@/composables/useI18n';
import { store as storeRoute } from '@/routes/admin/users';

const { t } = useI18n();

type RoleOption = {
    value: string;
    label: string;
};

const props = defineProps<{
    open: boolean;
    roles: RoleOption[];
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const form = useForm<{
    first_name: string;
    last_name: string;
    email: string;
    role: string;
}>({
    first_name: '',
    last_name: '',
    email: '',
    role: props.roles[0]?.value ?? 'admins',
});

function handleOpenChange(value: boolean) {
    if (!value && form.isDirty) {
        if (!confirm(t('app.admin.users.cancelConfirm'))) {
            return;
        }
    }
    if (!value) {
        form.reset();
        form.clearErrors();
    }
    emit('update:open', value);
}

function submit() {
    form.post(storeRoute.url(), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.clearErrors();
            emit('update:open', false);
        },
    });
}
</script>

<template>
    <Sheet :open="open" @update:open="handleOpenChange">
        <SheetContent class="w-full sm:max-w-md" side="right">
            <SheetHeader>
                <SheetTitle>{{ t('app.admin.users.inviteTitle') }}</SheetTitle>
            </SheetHeader>

            <div class="flex flex-col gap-4 px-4 py-6">
                <div class="grid gap-2">
                    <Label for="first_name">{{ t('app.admin.users.firstName') }} *</Label>
                    <Input id="first_name" v-model="form.first_name" :placeholder="t('app.admin.users.firstNamePlaceholder')" />
                    <InputError :message="form.errors.first_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="last_name">{{ t('app.admin.users.lastName') }} *</Label>
                    <Input id="last_name" v-model="form.last_name" :placeholder="t('app.admin.users.lastNamePlaceholder')" />
                    <InputError :message="form.errors.last_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">{{ t('app.admin.users.email') }} *</Label>
                    <Input id="email" v-model="form.email" type="email" dir="ltr" :placeholder="t('app.admin.users.emailPlaceholder')" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.admin.users.role') }} *</Label>
                    <Select
                        :model-value="form.role"
                        @update:model-value="(v) => (form.role = v ?? 'admins')"
                    >
                        <SelectTrigger>
                            <SelectValue :placeholder="t('app.admin.users.rolePlaceholder')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="role in roles" :key="role.value" :value="role.value">
                                {{ role.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.role" />
                </div>
            </div>

            <SheetFooter class="px-4 pb-6">
                <Button variant="outline" :disabled="form.processing" @click="emit('update:open', false)">
                    {{ t('app.admin.users.cancelBtn') }}
                </Button>
                <Button :disabled="form.processing" @click="submit">
                    <Spinner v-if="form.processing" />
                    {{ t('app.admin.users.sendInvitation') }}
                </Button>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>
