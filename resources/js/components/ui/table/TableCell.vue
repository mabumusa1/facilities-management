<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  colspan?: number
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <td :colspan="colspan" :class="cn('p-4 align-middle [&:has([role=checkbox])]:pr-0', props.class)">
    <slot />
  </td>
</template>
