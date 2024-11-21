<template>
  <div class="flex flex-wrap gap-4 mb-6 w-full">
    <Input v-model="search" placeholder="Search..." class=" md:w-64 flex-grow" />

    <Select v-model="selectedType">
      <SelectTrigger class="w-[180px]">
        <SelectValue placeholder="Select Type" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem value="">All Types</SelectItem>
        <SelectItem v-for="type in types" :key="type" :value="type">{{ type }}</SelectItem>
      </SelectContent>
    </Select>

    <Select v-model="selectedStyle">
      <SelectTrigger class="w-[180px]">
        <SelectValue placeholder="Select Style" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem value="">All Styles</SelectItem>
        <SelectItem v-for="style in styles" :key="style" :value="style">{{ style }}</SelectItem>
      </SelectContent>
    </Select>

    <Select v-model="selectedGender">
      <SelectTrigger class="w-[180px]">
        <SelectValue placeholder="Select Gender" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem value="">All Genders</SelectItem>
        <SelectItem value="male">Male</SelectItem>
        <SelectItem value="female">Female</SelectItem>
        <SelectItem value="unisex">Unisex</SelectItem>
      </SelectContent>
    </Select>

    <Button variant="outline" @click="resetFilters">Reset Filters</Button>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Button } from '@/components/ui/button'

const props = defineProps<{
  types: string[]
  styles: string[]
}>()

const emit = defineEmits(['update:filters'])

const search = ref('')
const selectedType = ref('')
const selectedStyle = ref('')
const selectedGender = ref('')

watch([search, selectedType, selectedStyle, selectedGender], () => {
  emit('update:filters', {
    search: search.value,
    type: selectedType.value,
    style: selectedStyle.value,
    gender: selectedGender.value
  })
})

function resetFilters() {
  search.value = ''
  selectedType.value = ''
  selectedStyle.value = ''
  selectedGender.value = ''
}
</script>