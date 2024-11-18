<template>
  <div class="text-center py-10">
    <component :is="icon" class="mx-auto h-12 w-12 text-muted-foreground" />
    <h3 class="mt-2 text-sm font-semibold text-muted-foreground">{{ title }}</h3>
    <p class="mt-1 text-sm text-muted-foreground">{{ description }}</p>
    <div class="mt-6">
      <Button v-if="actionLabel" @click="$emit('action')">
        {{ actionLabel }}
      </Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { ShirtIcon, FilterIcon } from 'lucide-vue-next'

const props = defineProps<{
  type: 'empty-wardrobe' | 'no-results'
}>()

defineEmits(['action'])

const config = {
  'empty-wardrobe': {
    icon: ShirtIcon,
    title: 'Your wardrobe is empty',
    description: 'Start adding clothes to your wardrobe!',
    actionLabel: 'Browse Available Clothes'
  },
  'no-results': {
    icon: FilterIcon,
    title: 'No matching items',
    description: 'Try adjusting your filters or search terms.',
    actionLabel: 'Reset Filters'
  }
}

const { icon, title, description, actionLabel } = config[props.type]
</script>