<template>
  <div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6">My Wardrobe</h1>

    <Tabs default-value="wardrobe" class="w-full mb-6">
      <TabsList>
        <TabsTrigger value="wardrobe">My Wardrobe</TabsTrigger>
        <TabsTrigger value="available">Available Clothing</TabsTrigger>
      </TabsList>
      <TabsContent value="wardrobe">
        <div v-if="wardrobe.length === 0" class="text-center py-8">
          <p class="text-xl text-gray-500">Your wardrobe is empty</p>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <ClothingItem
              v-for="item in wardrobe"
              :key="item.id"
              :item="item"
              @remove="removeFromWardrobe"
          />
        </div>
      </TabsContent>
      <TabsContent value="available">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <ClothingItem
              v-for="item in availableClothing"
              :key="item.id"
              :item="item"
              :is-available="true"
              @add="addToWardrobe"
          />
        </div>
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
import api from '@/api'

const wardrobe = ref([])
const availableClothing = ref([])
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

async function removeFromWardrobe(wardrobeId: number) {
  try {
    await removeClothingFromWardrobe(wardrobeId)
    await fetchWardrobe()
    await fetchAvailableClothing()
  } catch (err: any) {
    console.error('Error removing clothing from wardrobe:', err)
    error.value = 'Failed to remove clothing. Please try again.'
  }
}
</script>