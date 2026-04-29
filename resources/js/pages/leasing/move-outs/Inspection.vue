<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import {
    deductions as deductionsRoute,
    saveInspection as saveInspectionAction,
    uploadRoomPhoto as uploadPhotoAction,
    deleteRoomPhoto as deletePhotoAction,
} from '@/actions/App/Http/Controllers/Leasing/MoveOutController';
import { show as leasesShow } from '@/actions/App/Http/Controllers/Leasing/LeaseController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useI18n } from '@/composables/useI18n';

type Photo = { id: number; url: string; name: string };
type Room = {
    id: number | null;
    name: string;
    condition: string;
    notes: string;
    sort_order: number;
    photos: Photo[];
};

type ConditionOption = { value: string; label: string };

type MoveOutDetail = {
    id: number;
    status: { id: number; name_en: string | null } | null;
    rooms: Room[];
    progress: { inspected: number; total: number };
};

type LeaseRef = {
    id: number;
    contract_number: string;
};

const props = defineProps<{
    lease: LeaseRef;
    moveOut: MoveOutDetail;
    conditions: ConditionOption[];
}>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            { title: props.lease.contract_number, href: leasesShow.url(props.lease.id) },
            { title: t('app.moveout.inspection.title'), href: '#' },
        ],
    });
});

// Local editable copy of rooms.
const rooms = ref<Room[]>(
    props.moveOut.rooms.map((r) => ({ ...r, photos: [...r.photos] })),
);

const inspectedCount = computed(() => rooms.value.filter((r) => r.condition).length);
const progressPercent = computed(() =>
    rooms.value.length > 0 ? Math.round((inspectedCount.value / rooms.value.length) * 100) : 0,
);

const errors = ref<Record<string, string>>({});
const processing = ref(false);

function addRoom() {
    rooms.value.push({
        id: null,
        name: '',
        condition: '',
        notes: '',
        sort_order: rooms.value.length,
        photos: [],
    });
}

function removeRoom(index: number) {
    rooms.value.splice(index, 1);
}

function saveInspection() {
    errors.value = {};
    processing.value = true;

    router.post(
        saveInspectionAction.url(props.lease.id, props.moveOut.id),
        {
            rooms: rooms.value.map((r) => ({
                id: r.id,
                name: r.name,
                condition: r.condition,
                notes: r.notes,
                sort_order: r.sort_order,
            })),
        },
        {
            onError: (errs) => {
                errors.value = errs;
            },
            onFinish: () => {
                processing.value = false;
            },
        },
    );
}

function proceedToDeductions() {
    router.get(deductionsRoute.url(props.lease.id, props.moveOut.id));
}

// Photo upload
const fileInputs = ref<Record<number, HTMLInputElement | null>>({});

function triggerPhotoUpload(index: number) {
    fileInputs.value[index]?.click();
}

function handlePhotoUpload(index: number, event: Event) {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (! file) {
        return;
    }

    const room = rooms.value[index];
    if (! room.id) {
        return;
    }

    const formData = new FormData();
    formData.append('photo', file);

    fetch(uploadPhotoAction.url(props.lease.id, props.moveOut.id, room.id), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
        },
        body: formData,
    })
        .then((res) => res.json())
        .then((data: Photo) => {
            room.photos.push(data);
        });

    // Reset input so same file can be re-selected.
    input.value = '';
}

function removePhoto(roomIndex: number, photo: Photo) {
    const room = rooms.value[roomIndex];
    if (! room.id) {
        return;
    }

    fetch(deletePhotoAction.url(props.lease.id, props.moveOut.id, room.id, photo.id), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
        },
    }).then(() => {
        room.photos = room.photos.filter((p) => p.id !== photo.id);
    });
}
</script>

<template>
    <Head :title="t('app.moveout.inspection.title')" />

    <div class="space-y-6">
        <Heading
            :title="t('app.moveout.inspection.title')"
            :description="lease.contract_number"
        />

        <!-- Progress bar -->
        <div class="rounded-md border bg-card p-4">
            <div class="mb-2 flex items-center justify-between text-sm">
                <span>
                    {{
                        t('app.moveout.inspection.progress', {
                            n: inspectedCount,
                            total: rooms.length,
                        })
                    }}
                </span>
                <Badge variant="outline">{{ progressPercent }}%</Badge>
            </div>
            <div
                class="h-2 w-full overflow-hidden rounded-full bg-muted"
                role="progressbar"
                :aria-valuenow="inspectedCount"
                aria-valuemin="0"
                :aria-valuemax="rooms.length"
            >
                <div
                    class="h-full bg-primary transition-all"
                    :style="{ width: `${progressPercent}%` }"
                />
            </div>
        </div>

        <!-- Room sections -->
        <div class="space-y-4">
            <section
                v-for="(room, index) in rooms"
                :key="index"
                class="rounded-md border"
                :aria-label="`${room.name || t('app.moveout.inspection.addRoom')} inspection`"
            >
                <Card class="shadow-none">
                    <CardHeader class="flex flex-row items-center justify-between pb-3">
                        <div class="flex-1">
                            <Input
                                v-model="room.name"
                                :placeholder="t('app.moveout.inspection.addRoom')"
                                class="max-w-xs font-medium"
                            />
                            <InputError
                                v-if="errors[`rooms.${index}.name`]"
                                :message="errors[`rooms.${index}.name`]"
                            />
                        </div>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="ms-2 text-destructive"
                            @click="removeRoom(index)"
                        >
                            ×
                        </Button>
                    </CardHeader>

                    <CardContent class="space-y-4">
                        <!-- Condition -->
                        <div class="space-y-2">
                            <Label :for="`condition_${index}`">
                                {{ t('app.moveout.inspection.condition') }}
                            </Label>
                            <Select v-model="room.condition">
                                <SelectTrigger :id="`condition_${index}`">
                                    <SelectValue :placeholder="t('app.moveout.inspection.condition')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="opt in conditions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError
                                v-if="errors[`rooms.${index}.condition`]"
                                :message="errors[`rooms.${index}.condition`]"
                            />
                        </div>

                        <!-- Notes -->
                        <div class="space-y-2">
                            <Label :for="`notes_${index}`">
                                {{ t('app.moveout.inspection.notes') }}
                            </Label>
                            <Textarea
                                :id="`notes_${index}`"
                                v-model="room.notes"
                                rows="2"
                            />
                        </div>

                        <!-- Photos -->
                        <div class="space-y-2">
                            <Label>{{ t('app.moveout.inspection.photos') }}</Label>
                            <div class="flex flex-wrap gap-2">
                                <div
                                    v-for="photo in room.photos"
                                    :key="photo.id"
                                    class="group relative"
                                >
                                    <img
                                        :src="`/storage/${photo.url}`"
                                        :alt="photo.name"
                                        class="size-16 rounded border object-cover"
                                    />
                                    <button
                                        class="absolute -right-1 -top-1 hidden size-5 items-center justify-center rounded-full bg-destructive text-xs text-white group-hover:flex"
                                        type="button"
                                        :aria-label="`Remove ${photo.name}`"
                                        @click="removePhoto(index, photo)"
                                    >
                                        ×
                                    </button>
                                </div>
                                <template v-if="room.id">
                                    <input
                                        :ref="(el) => (fileInputs[index] = el as HTMLInputElement)"
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handlePhotoUpload(index, $event)"
                                    />
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        @click="triggerPhotoUpload(index)"
                                    >
                                        + {{ t('app.moveout.inspection.addPhoto') }}
                                    </Button>
                                </template>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </section>
        </div>

        <!-- Add room button -->
        <Button
            type="button"
            variant="outline"
            @click="addRoom"
        >
            + {{ t('app.moveout.inspection.addRoom') }}
        </Button>

        <!-- Footer actions -->
        <div class="flex items-center justify-between gap-3">
            <Button
                type="button"
                variant="outline"
                :disabled="processing"
                @click="saveInspection"
            >
                {{ t('app.moveout.inspection.save') }}
            </Button>
            <Button
                type="button"
                :disabled="processing"
                @click="proceedToDeductions"
            >
                {{ t('app.moveout.inspection.proceedToDeductions') }} →
            </Button>
        </div>
    </div>
</template>
