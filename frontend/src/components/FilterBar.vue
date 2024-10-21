<template>
  <div class="flex flex-wrap gap-4 mb-6">
    <Select v-model="selectedType" @update:modelValue="updateFilters">
      <SelectTrigger class="w-[180px]">
        <SelectValue placeholder="Select Type" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem :value="null">All Types</SelectItem> <!-- Use null instead of an empty string -->
        <SelectItem v-for="type in types" :key="type" :value="type">
          {{ type }}
        </SelectItem>
      </SelectContent>
    </Select>

    <Select v-model="selectedStyle" @update:modelValue="updateFilters">
      <SelectTrigger class="w-[180px]">
        <SelectValue placeholder="Select Style" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem :value="null">All Styles</SelectItem> <!-- Use null instead of an empty string -->
        <SelectItem v-for="style in styles" :key="style" :value="style">
          {{ style }}
        </SelectItem>
      </SelectContent>
    </Select>

    <Button variant="outline" @click="resetFilters">
      <RefreshCcw class="mr-2 h-4 w-4" />
      Reset Filters
    </Button>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Button } from '@/components/ui/button'
import { RefreshCcw } from 'lucide-vue-next'

const props = defineProps<{
  types: string[]
  styles: string[]
}>()

const emit = defineEmits<{
  (e: 'update:filters', filters: { type: string; style: string }): void
}>()

const selectedType = ref('')
const selectedStyle = ref('')

function updateFilters() {
  emit('update:filters', {
    type: selectedType.value ?? '',
    style: selectedStyle.value ?? ''
  })
}

function resetFilters() {
  selectedType.value = ''
  selectedStyle.value = ''
  updateFilters()
}

watch([() => props.types, () => props.styles], () => {
  resetFilters()
})
</script>