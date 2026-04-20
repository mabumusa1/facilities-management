<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { PaginationLink } from '@/types';
import { Button } from '@/components/ui/button';

export type Column<T> = {
    key: string;
    label: string;
    render?: (row: T) => string | number;
};

defineProps<{
    columns: Column<unknown>[];
    rows: unknown[];
    rowHref?: (row: unknown) => string;
    links?: PaginationLink[];
    emptyMessage?: string;
}>();

function getCellValue(row: Record<string, unknown>, col: Column<unknown>): unknown {
    if (col.render) {
        return col.render(row);
    }
    const keys = col.key.split('.');
    let value: unknown = row;
    for (const key of keys) {
        if (value == null) {
            return '—';
        }
        value = (value as Record<string, unknown>)[key];
    }
    return value ?? '—';
}
</script>

<template>
    <div class="space-y-4">
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead v-for="col in columns" :key="col.key">
                            {{ col.label }}
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-if="rows.length === 0">
                        <TableCell
                            :colspan="columns.length"
                            class="text-muted-foreground h-24 text-center"
                        >
                            {{ emptyMessage || 'No results found.' }}
                        </TableCell>
                    </TableRow>
                    <template v-for="(row, index) in rows" :key="index">
                        <TableRow
                            class="cursor-pointer"
                            v-if="rowHref"
                        >
                            <TableCell v-for="col in columns" :key="col.key">
                                <Link
                                    :href="rowHref(row)"
                                    class="block"
                                >
                                    {{ getCellValue(row as Record<string, unknown>, col) }}
                                </Link>
                            </TableCell>
                        </TableRow>
                        <TableRow v-else>
                            <TableCell v-for="col in columns" :key="col.key">
                                {{ getCellValue(row as Record<string, unknown>, col) }}
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>

        <!-- Pagination -->
        <div v-if="links && links.length > 3" class="flex items-center justify-end gap-1">
            <template v-for="link in links" :key="link.label">
                <Button
                    v-if="link.url"
                    variant="outline"
                    size="sm"
                    as-child
                    :class="{ 'bg-primary text-primary-foreground': link.active }"
                >
                    <Link :href="link.url" v-html="link.label" />
                </Button>
                <Button
                    v-else
                    variant="outline"
                    size="sm"
                    disabled
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
