<template>
  <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <h2 class="text-2xl font-semibold text-primary mb-2">{{ title }}</h2>
    <p class="text-muted-foreground">{{ description }}</p>
  </div>
</template>

<script setup lang="ts">

const props = defineProps({
  icon: {
    type: String,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  }
})
</script>