<template>
  <div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-primary">My Wardrobe</h1>

    <Tabs default-value="wardrobe" class="w-full mb-6">
      <TabsList class="w-full mb-6">
        <TabsTrigger value="wardrobe" class="flex-1">My Wardrobe</TabsTrigger>
        <TabsTrigger value="available" class="flex-1">Available Clothing</TabsTrigger>
      </TabsList>
      <TabsContent value="wardrobe">
        <TransitionGroup
            name="list"
            tag="div"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
        >
          <div v-if="wardrobe.length === 0" key="empty" class="col-span-full">
            <EmptyState
                icon="Hanger"
                title="Your wardrobe is empty"
                description="Add some clothes to get started!"
            />
          </div>
          <ClothingItem
              v-else
              v-for="item in wardrobe"
              :key="item.id"
              :item="item.clothing"
              :is-available="false"
              @remove="removeFromWardrobe(item.clothing.id)"
          />
        </TransitionGroup>
      </TabsContent>
      <TabsContent value="available">
        <TransitionGroup
            name="list"
            tag="div"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
        >
          <div v-if="availableClothing.length === 0" key="empty" class="col-span-full">
            <EmptyState
                icon="ShoppingBag"
                title="No available clothing"
                description="Check back later for new arrivals!"
            />
          </div>
          <ClothingItem
              v-else
              v-for="item in availableClothing"
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
import { ref, onMounted } from 'vue'
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs'
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert'
import ClothingItem from './ClothingItem.vue'
import EmptyState from './EmptyState.vue'
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
</script>

<style scoped>

</style>