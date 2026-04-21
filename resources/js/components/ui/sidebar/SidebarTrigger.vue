<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { PanelLeftClose, PanelLeftOpen, PanelRightClose, PanelRightOpen } from "lucide-vue-next"
import { computed } from "vue"
import { cn } from "@/lib/utils"
import { Button } from '@/components/ui/button'
import { useI18n } from "@/composables/useI18n"
import { useSidebar } from "./utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()

const { isMobile, state, toggleSidebar } = useSidebar()
const { isArabic } = useI18n()

const triggerIcon = computed(() => {
  const collapsed = isMobile.value || state.value === 'collapsed'
  if (isArabic.value) {
    return collapsed ? PanelRightOpen : PanelRightClose
  }
  return collapsed ? PanelLeftOpen : PanelLeftClose
})
</script>

<template>
  <Button
    data-sidebar="trigger"
    data-slot="sidebar-trigger"
    variant="ghost"
    size="icon"
    :class="cn('h-7 w-7', props.class)"
    @click="toggleSidebar"
  >
    <component :is="triggerIcon" />
    <span class="sr-only">Toggle sidebar</span>
  </Button>
</template>
