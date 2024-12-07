<template>
  <div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-primary">My Wardrobe</h1>

    <div v-if="loading" class="text-center">
      <Loader2Icon class="animate-spin h-8 w-8 mx-auto" />
      <p>Loading your wardrobe...</p>
    </div>

    <div v-else>
      <div ref="filterBarContainer" class="mb-6">
        <FilterBar
            ref="filterBar"
            :types="allTypes"
            :styles="allStyles"
            :materials="allMaterials"
            :colors="allColors"
            @update:filters="updateFilters"
            :class="[
            { 'fixed top-[60px] left-0 right-0 z-10 shadow-md bg-background': isFilterBarSticky },
            isFilterBarSticky ? 'px-4 py-2' : 'mb-6',
            'container mx-auto'
          ]"
            :style="isFilterBarSticky ? { width: contentWidth } : {}"
        />
        <div v-if="isFilterBarSticky" :style="{ height: filterBarHeight + 'px' }"></div>
      </div>

      <div class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">My Clothing</h2>
        <TransitionGroup
            name="list"
            tag="div"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
        >
          <template v-if="filteredWardrobe.length === 0">
            <EmptyState
                v-if="wardrobe.length === 0"
                key="empty-wardrobe"
                type="empty-wardrobe"
                @action="scrollToAvailable"
            />
            <EmptyState
                v-else
                key="no-results-wardrobe"
                type="no-results"
                @action="resetFilters"
            />
          </template>
          <ClothingCard
              v-else
              v-for="item in filteredWardrobe"
              :key="item.id"
              :item="item.clothing"
              :is-in-wardrobe="true"
              @remove="removeFromWardrobe"
          />
        </TransitionGroup>
      </div>

      <div ref="availableSection">
        <h2 class="text-2xl font-semibold mb-4">Suggested Clothing</h2>
        <TransitionGroup
            name="list"
            tag="div"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
        >
          <EmptyState
              v-if="filteredAvailableClothing.length === 0"
              key="no-results-available"
              type="no-results"
              @action="resetFilters"
          />
          <ClothingCard
              v-else
              v-for="item in filteredAvailableClothing"
              :key="item.id"
              :item="item"
              :is-in-wardrobe="false"
              @add="addToWardrobe"
          />
        </TransitionGroup>
      </div>
    </div>

    <Alert v-if="error" variant="destructive" class="mt-4">
      <AlertTitle>Error</AlertTitle>
      <AlertDescription>{{ error }}</AlertDescription>
    </Alert>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue'
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert'
import { Loader2Icon } from 'lucide-vue-next'
import ClothingCard from './ClothingCard.vue'
import EmptyState from './EmptyState.vue'
import FilterBar from './FilterBar.vue'
import api from '@/api'

interface WardrobeItem {
  id: number
  wardrobe_id: number
  clothing_id: number
  created_at: string
  updated_at: string
  clothing: ClothingItem
}

interface ClothingItem {
  id: number
  style_id: number
  photo_id: number | null
  type_id: number
  temperature_range_id: number
  material_id: number
  gender: string
  color: string
  water_resistant: number
  created_at: string
  updated_at: string
  type: { id: number; type: string }
  material: { id: number; material: string }
  style: { id: number; style: string }
  photo?: { id: number; photo_url: string }
}

const wardrobe = ref<WardrobeItem[]>([])
const availableClothing = ref<ClothingItem[]>([])
const error = ref<string | null>(null)
const loading = ref(true)
const availableSection = ref<HTMLElement | null>(null)
const filterBarContainer = ref<HTMLElement | null>(null)
const filterBar = ref<InstanceType<typeof FilterBar> | null>(null)
const isFilterBarSticky = ref(false)
const filterBarHeight = ref(0)

const filters = ref({
  search: '',
  type: null as string | null,
  style: null as string | null,
  material: null as string | null,
  color: null as string | null
})

const allTypes = computed(() => {
  const types = new Set<string>()
  wardrobe.value.forEach(item => types.add(item.clothing.type.type))
  availableClothing.value.forEach(item => types.add(item.type.type))
  return Array.from(types)
})

const allStyles = computed(() => {
  const styles = new Set<string>()
  wardrobe.value.forEach(item => styles.add(item.clothing.style.style))
  availableClothing.value.forEach(item => styles.add(item.style.style))
  return Array.from(styles)
})

const allMaterials = computed(() => {
  const materials = new Set<string>()
  wardrobe.value.forEach(item => materials.add(item.clothing.material.material))
  availableClothing.value.forEach(item => materials.add(item.material.material))
  return Array.from(materials)
})

const allColors = computed(() => {
  const colors = new Set<string>()
  wardrobe.value.forEach(item => colors.add(item.clothing.color))
  availableClothing.value.forEach(item => colors.add(item.color))
  return Array.from(colors)
})

const filteredWardrobe = computed(() => {
  return wardrobe.value.filter(item =>
      itemMatchesFilters(item.clothing, filters.value)
  )
})

const filteredAvailableClothing = computed(() => {
  return availableClothing.value.filter(item =>
      itemMatchesFilters(item, filters.value) &&
      !wardrobe.value.some(wardrobeItem => wardrobeItem.clothing_id === item.id)
  )
})

function itemMatchesFilters(item: ClothingItem, filters: { search: string, type: string | null, style: string | null, material: string | null, color: string | null }): boolean {
  const searchLower = filters.search.toLowerCase()
  return (
      (!filters.search ||
          item.type.type.toLowerCase().includes(searchLower) ||
          item.material.material.toLowerCase().includes(searchLower) ||
          item.style.style.toLowerCase().includes(searchLower) ||
          item.color.toLowerCase().includes(searchLower)
      ) &&
      (!filters.type || item.type.type === filters.type) &&
      (!filters.style || item.style.style === filters.style) &&
      (!filters.material || item.material.material === filters.material) &&
      (!filters.color || item.color === filters.color)
  )
}

const contentWidth = computed(() => {
  return filterBarContainer.value ? `${filterBarContainer.value.offsetWidth}px` : 'auto'
})

onMounted(async () => {
  await fetchWardrobe()
  await fetchAvailableClothing()
  loading.value = false

  nextTick(() => {
    const observer = new IntersectionObserver(
        ([entry]) => {
          isFilterBarSticky.value = !entry.isIntersecting
        },
        { threshold: [1], rootMargin: "-60px 0px 0px 0px" }
    )

    if (filterBarContainer.value) {
      observer.observe(filterBarContainer.value)
    }
    if (filterBar.value?.$el) {
      filterBarHeight.value = filterBar.value.$el.offsetHeight
    }
  })
})

async function fetchWardrobe() {
  try {
    const response = await api.get('api/wardrobe')
    wardrobe.value = response.data
  } catch (err: any) {
    console.error('Error fetching wardrobe:', err)
    error.value = 'Failed to fetch wardrobe. Please try again.'
  }
}

async function fetchAvailableClothing() {
  try {
    const response = await api.get('api/available-clothing')
    availableClothing.value = response.data
  } catch (err: any) {
    console.error('Error fetching available clothing:', err)
    error.value = 'Failed to fetch available clothing. Please try again.'
  }
}

async function addToWardrobe(clothingId: number) {
  try {
    await api.post('api/wardrobe', { clothing_id: clothingId })
    await fetchWardrobe()
  } catch (err: any) {
    console.error('Error adding clothing to wardrobe:', err)
    error.value = 'Failed to add clothing. Please try again.'
  }
}

async function removeFromWardrobe(itemId: number) {
  try {
    await api.delete(`api/wardrobe/${itemId}`)
    await fetchWardrobe()
  } catch (err: any) {
    console.error('Error removing clothing from wardrobe:', err)
    error.value = 'Failed to remove clothing. Please try again.'
  }
}

function updateFilters(newFilters: { search: string, type: string | null, style: string | null, material: string | null, color: string | null }) {
  filters.value = newFilters
}

function resetFilters() {
  filters.value = {
    search: '',
    type: null,
    style: null,
    material: null,
    color: null
  }
}

function scrollToAvailable() {
  if (availableSection.value) {
    availableSection.value.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateY(30px);
}
</style>