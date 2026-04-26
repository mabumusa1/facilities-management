<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { CheckCircle } from 'lucide-vue-next';
</script>

<template>
    <Head title="Document Signed" />
    <div class="min-h-screen bg-muted/30 flex items-center justify-center p-4">
        <div class="text-center py-12">
            <CheckCircle class="h-12 w-12 text-green-500 mx-auto mb-4" />
            <h1 class="text-xl font-semibold mb-2">Already Signed</h1>
            <p class="text-muted-foreground text-sm">This document has already been signed.</p>
        </div>
    </div>
</template>
