<script setup lang="ts">
import { ref } from 'vue'
import { Button } from '@/components/ui/button'

defineProps<{ msg: string }>()

const count = ref(0)
</script>

<template>
  <h1 class="m-8">Test</h1>

  <div class="card">
    <Button class="mx-8" type="button" @click="count++">count is {{ count }}</Button>
  </div>
</template>

<style scoped>

</style>
