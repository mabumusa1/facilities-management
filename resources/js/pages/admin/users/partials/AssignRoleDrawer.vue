<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { useI18n } from '@/composables/useI18n';
import { store as storeRoute } from '@/routes/admin/users/role-assignments';

const { t } = useI18n();

type Role = {
    id: number;
    name: string;
    name_en: string | null;
    name_ar: string | null;
    scope_level: 'none' | 'manager' | 'serviceManager';
};

type Community = {
    id: number;
    name: string;
};

type Building = {
    id: number;
    name: string;
    rf_community_id: number | null;
};

type ServiceType = {
    id: number;
    name: string;
};

type User = {
    id: number;
    name: string;
    email: string;
};

const props = defineProps<{
    open: boolean;
    user: User;
    roles: Role[];
    communities: Community[];
    buildings: Building[];
    serviceTypes: ServiceType[];
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const form = useForm<{
    role_id: number | null;
    community_id: number | null;
    building_id: number | null;
    service_type_id: number | null;
}>({
    role_id: null,
    community_id: null,
    building_id: null,
    service_type_id: null,
});

const selectedRole = computed<Role | undefined>(() => {
    if (form.role_id === null) {
        return undefined;
    }
    return props.roles.find((r) => r.id === form.role_id);
});

const scopeLevel = computed<'none' | 'manager' | 'serviceManager'>(() => {
    return selectedRole.value?.scope_level ?? 'none';
});

const filteredBuildings = computed<Building[]>(() => {
    if (form.community_id === null) {
        return props.buildings;
    }
    return props.buildings.filter((b) => b.rf_community_id === form.community_id);
});

// Reset scope fields when role changes
watch(
    () => form.role_id,
    () => {
        form.community_id = null;
        form.building_id = null;
        form.service_type_id = null;
        form.clearErrors();
    },
);

function close() {
    emit('update:open', false);
}

function handleOpenChange(value: boolean) {
    if (!value && form.isDirty) {
        if (!confirm(t('app.admin.users.cancelConfirm') || 'Discard changes?')) {
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
    form.post(storeRoute.url({ user: props.user.id }), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.clearErrors();
            emit('update:open', false);
        },
    });
}

function roleLabel(role: Role): string {
    return role.name_en ?? role.name;
}
</script>

<template>
    <Sheet :open="open" @update:open="handleOpenChange">
        <SheetContent class="w-full sm:max-w-md" side="right">
            <SheetHeader>
                <SheetTitle>{{ t('app.admin.users.drawerTitle') }}</SheetTitle>
            </SheetHeader>

            <div class="flex flex-col gap-4 px-4 py-6">
                <!-- Role picker -->
                <div class="flex flex-col gap-2">
                    <Label>{{ t('app.admin.users.roleLabel') }} *</Label>
                    <Select
                        :model-value="form.role_id !== null ? String(form.role_id) : undefined"
                        @update:model-value="(v) => (form.role_id = v ? Number(v) : null)"
                    >
                        <SelectTrigger>
                            <SelectValue :placeholder="t('app.admin.users.rolePlaceholder')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="role in roles" :key="role.id" :value="String(role.id)">
                                {{ roleLabel(role) }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.role_id" />
                </div>

                <!-- Global scope note -->
                <div v-if="form.role_id !== null && scopeLevel === 'none'" class="text-muted-foreground rounded-md border p-3 text-sm">
                    {{ t('app.admin.users.globalScopeNote') }}
                </div>

                <!-- Community selector -->
                <div
                    v-if="form.role_id !== null && (scopeLevel === 'manager' || scopeLevel === 'serviceManager')"
                    class="flex flex-col gap-2"
                >
                    <Label>{{ t('app.admin.users.communityLabel') }} *</Label>
                    <Select
                        :model-value="form.community_id !== null ? String(form.community_id) : undefined"
                        @update:model-value="(v) => { form.community_id = v ? Number(v) : null; form.building_id = null; }"
                    >
                        <SelectTrigger>
                            <SelectValue :placeholder="t('app.admin.users.communityPlaceholder')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="community in communities" :key="community.id" :value="String(community.id)">
                                {{ community.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.community_id" />
                </div>

                <!-- Building selector -->
                <div
                    v-if="form.role_id !== null && (scopeLevel === 'manager' || scopeLevel === 'serviceManager')"
                    class="flex flex-col gap-2"
                >
                    <Label>{{ t('app.admin.users.buildingLabel') }}</Label>
                    <Select
                        :model-value="form.building_id !== null ? String(form.building_id) : undefined"
                        @update:model-value="(v) => (form.building_id = v ? Number(v) : null)"
                    >
                        <SelectTrigger>
                            <SelectValue :placeholder="t('app.admin.users.buildingPlaceholder')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="building in filteredBuildings" :key="building.id" :value="String(building.id)">
                                {{ building.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p class="text-muted-foreground text-xs">{{ t('app.admin.users.buildingOptionalHint') }}</p>
                    <InputError :message="form.errors.building_id" />
                </div>

                <!-- Service type selector -->
                <div v-if="form.role_id !== null && scopeLevel === 'serviceManager'" class="flex flex-col gap-2">
                    <Label>{{ t('app.admin.users.serviceTypeLabel') }} *</Label>
                    <Select
                        :model-value="form.service_type_id !== null ? String(form.service_type_id) : undefined"
                        @update:model-value="(v) => (form.service_type_id = v ? Number(v) : null)"
                    >
                        <SelectTrigger>
                            <SelectValue :placeholder="t('app.admin.users.serviceTypePlaceholder')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="st in serviceTypes" :key="st.id" :value="String(st.id)">
                                {{ st.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.service_type_id" />
                </div>
            </div>

            <SheetFooter class="px-4 pb-6">
                <Button variant="outline" :disabled="form.processing" @click="close">
                    {{ t('app.admin.users.cancelBtn') }}
                </Button>
                <Button :disabled="form.processing || form.role_id === null" @click="submit">
                    {{ form.processing ? '…' : t('app.admin.users.assignBtn') }}
                </Button>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>
