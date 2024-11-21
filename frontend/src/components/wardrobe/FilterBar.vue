<template>
  <div class="flex flex-wrap gap-4 mb-6">
    <div class="flex-grow">
      <Input
          v-model="searchQuery"
          placeholder="Search clothing..."
          class="w-full"
          @input="onSearchInput"
      />
    </div>
    <Select v-model="selectedType" @update:modelValue="updateFilters">
      <SelectTrigger class="w-[120px]">
        <SelectValue/>
      </SelectTrigger>
      <SelectContent>
        <SelectItem :value="null">All Types</SelectItem>
        <SelectItem v-for="type in types" :key="type" :value="type">
          {{ type }}
        </SelectItem>
      </SelectContent>
    </Select>
    <Select v-model="selectedStyle" @update:modelValue="updateFilters">
      <SelectTrigger class="w-[120px]">
        <SelectValue/>
      </SelectTrigger>
      <SelectContent>
        <SelectItem :value="null">All Styles</SelectItem>
        <SelectItem v-for="style in styles" :key="style" :value="style">
          {{ style }}
        </SelectItem>
      </SelectContent>
    </Select>
    <Select v-model="selectedMaterial" @update:modelValue="updateFilters">
      <SelectTrigger class="w-[120px]">
        <SelectValue/>
      </SelectTrigger>
      <SelectContent>
        <SelectItem :value="null">All Materials</SelectItem>
        <SelectItem v-for="material in materials" :key="material" :value="material">
          {{ material }}
        </SelectItem>
      </SelectContent>
    </Select>
    <Select v-model="selectedColor" @update:modelValue="updateFilters">
      <SelectTrigger class="w-[120px]">
        <SelectValue/>
      </SelectTrigger>
      <SelectContent>
        <SelectItem :value="null">All Colors</SelectItem>
        <SelectItem v-for="color in colors" :key="color" :value="color">
          {{ color }}
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
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { RefreshCcw } from 'lucide-vue-next'

const props = defineProps<{
  types: string[]
  styles: string[]
  materials: string[]
  colors: string[]
}>()

const emit = defineEmits<{
  (e: 'update:filters', filters: { type: string | null; style: string | null; material: string | null; color: string | null; search: string }): void
}>()

const selectedType = ref<string | null>(null)
const selectedStyle = ref<string | null>(null)
const selectedMaterial = ref<string | null>(null)
const selectedColor = ref<string | null>(null)
const searchQuery = ref('')

function onSearchInput(event: Event) {
  searchQuery.value = (event.target as HTMLInputElement).value
  updateFilters()
}

function updateFilters() {
  emit('update:filters', {
    type: selectedType.value,
    style: selectedStyle.value,
    material: selectedMaterial.value,
    color: selectedColor.value,
    search: searchQuery.value
  })
}

function resetFilters() {
  selectedType.value = null
  selectedStyle.value = null
  selectedMaterial.value = null
  selectedColor.value = null
  searchQuery.value = ''
  updateFilters()
}

watch(() => props.types, () => {
  resetFilters()
})

watch(() => props.styles, () => {
  resetFilters()
})

watch(() => props.materials, () => {
  resetFilters()
})

watch(() => props.colors, () => {
  resetFilters()
})
</script>