<template>
  <div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-primary">My Wardrobe</h1>

    <Tabs v-model="activeTab" class="w-full mb-6">
      <TabsList class="w-full mb-6">
        <TabsTrigger value="wardrobe" class="flex-1">My Wardrobe</TabsTrigger>
        <TabsTrigger value="available" class="flex-1">Available Clothing</TabsTrigger>
      </TabsList>
      <TabsContent value="wardrobe">
        <FilterBar
            :types="allTypes"
            :styles="allStyles"
            @update:filters="updateFilters"
        />
        <TransitionGroup
            name="list"
            tag="div"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
        >
          <div v-if="filteredWardrobe.length === 0" key="empty" class="col-span-full">
            <EmptyState
                icon="Hanger"
                :title="wardrobe.length === 0 ? 'Your wardrobe is empty' : 'No items match the current filters'"
                :description="wardrobe.length === 0 ? 'Add some clothes to get started!' : 'Try adjusting your filters'"
            />
          </div>
          <ClothingItem
              v-else
              v-for="item in filteredWardrobe"
              :key="item.id"
              :item="item.clothing"
              :is-available="false"
              @remove="removeFromWardrobe(item.clothing.id)"
          />
        </TransitionGroup>
      </TabsContent>
      <TabsContent value="available">
        <FilterBar
            :types="allTypes"
            :styles="allStyles"
            @update:filters="updateFilters"
        />
        <TransitionGroup
            name="list"
            tag="div"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
        >
          <div v-if="filteredAvailableClothing.length === 0" key="empty" class="col-span-full">
            <EmptyState
                icon="ShoppingBag"
                :title="availableClothing.length === 0 ? 'No available clothing' : 'No items match the current filters'"
                :description="availableClothing.length === 0 ? 'Check back later for new arrivals!' : 'Try adjusting your filters'"
            />
          </div>
          <ClothingItem
              v-else
              v-for="item in filteredAvailableClothing"
              :key="item.id"
              :item="item"
              :is-available="true"
              @add="addToWardrobe(item.id)"
          />
        </TransitionGroup>
      </TabsContent>
    </Tabs>

    <Alert v-if="error" variant="destructive">
      <AlertTitle>Error</AlertTitle>
      <AlertDescription>{{ error }}</AlertDescription>
    </Alert>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs'
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert'
import ClothingItem from './ClothingItem.vue'
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
}

const wardrobe = ref<WardrobeItem[]>([])
const availableClothing = ref<ClothingItem[]>([])
const error = ref<string | null>(null)
const activeTab = ref('wardrobe')

const filters = ref({
  type: '',
  style: '',
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

const filteredWardrobe = computed(() => {
  return wardrobe.value.filter(item =>
      (!filters.value.type || item.clothing.type.type === filters.value.type) &&
      (!filters.value.style || item.clothing.style.style === filters.value.style)
  )
})

const filteredAvailableClothing = computed(() => {
  return availableClothing.value.filter(item =>
      (!filters.value.type || item.type.type === filters.value.type) &&
      (!filters.value.style || item.style.style === filters.value.style)
  )
})

onMounted(async () => {
  await fetchWardrobe()
  await fetchAvailableClothing()
})

async function getWardrobe() {
  return api.get('api/wardrobe')
}

async function getAvailableClothing() {
  return api.get('api/available-clothing')
}

async function addClothingToWardrobe(clothingId: number) {
  return api.post('api/wardrobe', { clothing_id: clothingId })
}

async function removeClothingFromWardrobe(wardrobeId: number) {
  return api.delete(`api/wardrobe/${wardrobeId}`)
}

async function fetchWardrobe() {
  try {
    const response = await getWardrobe()
    wardrobe.value = response.data
  } catch (err: any) {
    console.error('Error fetching wardrobe:', err)
    error.value = 'Failed to fetch wardrobe. Please try again.'
  }
}

async function fetchAvailableClothing() {
  try {
    const response = await getAvailableClothing()
    availableClothing.value = response.data
  } catch (err: any) {
    console.error('Error fetching available clothing:', err)
    error.value = 'Failed to fetch available clothing. Please try again.'
  }
}

async function addToWardrobe(clothingId: number) {
  try {
    await addClothingToWardrobe(clothingId)
    await fetchWardrobe()
    await fetchAvailableClothing()
  } catch (err: any) {
    console.error('Error adding clothing to wardrobe:', err)
    error.value = 'Failed to add clothing. Please try again.'
  }
}

async function removeFromWardrobe(itemId: number) {
  try {
    await removeClothingFromWardrobe(itemId)
    await fetchWardrobe()
    await fetchAvailableClothing()
  } catch (err: any) {
    console.error('Error removing clothing from wardrobe:', err)
    error.value = 'Failed to remove clothing. Please try again.'
  }
}

function updateFilters(newFilters: { type: string; style: string }) {
  filters.value = newFilters
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