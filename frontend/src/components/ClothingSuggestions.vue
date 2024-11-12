<template>
  <div class="clothing-suggestions h-screen overflow-y-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Clothing Suggestions</h2>
    <Alert v-if="error" variant="destructive" class="mb-4">
      <CircleAlert class="h-4 w-4" />
      <AlertTitle>Error</AlertTitle>
      <AlertDescription>
        {{ error }}
      </AlertDescription>
    </Alert>
    <div v-if="isLoading" class="flex justify-center items-center h-32">
      <Loader class="h-8 w-8 animate-spin" />
    </div>
    <div v-else-if="suggestions">
      <div v-if="suggestions.clothing_suggestions && typeof suggestions.clothing_suggestions === 'object'">
        <div class="grid grid-cols-2 gap-4">
          <div v-for="(categoryItems, category) in suggestions.clothing_suggestions" :key="category" class="space-y-2">
            <div v-if="categoryItems && categoryItems.length > 0">
              <h3 class="flex items-center space-x-2 text-sm font-bold mb-2 text-gray-800 dark:text-gray-200">
                <component :is="getLayerIcon(category)" class="h-4 w-4" />
                <span class="capitalize">{{ category }}</span>
              </h3>
              <div class="flex justify-center px-8">
                <Carousel class="w-[275px]" @init-api="(api) => setApi(category, api)"> <!-- TODO: fix different arrow height -->
                  <CarouselContent>
                    <CarouselItem v-for="(item, index) in categoryItems" :key="index">
                      <Card class="overflow-hidden transition-all duration-300 hover:shadow-lg">
                        <CardHeader class="p-2">
                          <CardTitle class="text-xs font-bold">{{ item.style }} {{ item.type }}</CardTitle>
                        </CardHeader>
                        <CardContent class="p-2">
                          <div class="aspect-square relative mb-2 overflow-hidden flex items-center justify-center">
                            <img v-if="item.photo" :src="item.photo" :alt="item.type" class="w-full h-full object-cover" />
                            <div v-else class="text-2xl font-bold text-gray-400 dark:text-gray-600">
                              {{ item.type.charAt(0).toUpperCase() }}
                            </div>
                          </div>
                          <div class="grid grid-cols-2 gap-1 text-[10px]">
                            <div class="flex items-center space-x-1">
                              <span class="font-medium text-gray-600 dark:text-gray-400">Material:</span>
                              <span class="text-gray-800 dark:text-gray-200">{{ item.material }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                              <span class="font-medium text-gray-600 dark:text-gray-400">Color:</span>
                              <div class="flex items-center">
                                <div
                                    class="w-2 h-2 rounded-full mr-1 border border-gray-300 dark:border-gray-600"
                                    :style="{ backgroundColor: item.color }"
                                ></div>
                                <span class="capitalize text-gray-800 dark:text-gray-200">{{ item.color }}</span>
                              </div>
                            </div>
                            <div v-if="item.water_resistant === 1" class="flex items-center space-x-1 col-span-2">
                              <Droplet class="h-2 w-2 text-blue-500" />
                              <span class="text-blue-500">Water Resistant</span>
                            </div>
                          </div>
                        </CardContent>
                      </Card>
                    </CarouselItem>
                  </CarouselContent>
                  <CarouselPrevious class="h-6 w-6" />
                  <CarouselNext class="h-6 w-6" />
                </Carousel>
              </div>
              <div class="text-center text-[10px] text-muted-foreground mt-1">
                {{ carouselStates[category]?.current || 1 }} / {{ carouselStates[category]?.total || categoryItems.length }}
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="mt-4 text-center text-destructive">
        {{ suggestions.message || 'No specific clothing suggestions available.' }}
      </div>
    </div>
    <div v-else class="mt-4 text-center text-destructive">
      No clothing suggestions available.
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, reactive } from 'vue'
import api from '@/api'
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { CircleAlert, Loader, Droplet, Layers } from 'lucide-vue-next'
import { Carousel, CarouselContent, CarouselItem, CarouselPrevious, CarouselNext } from '@/components/ui/carousel'
import type { CarouselApi } from '@/components/ui/carousel'

// Interfaces for TypeScript
interface ClothingItem {
  type: string;
  style: string;
  material: string;
  color: string;
  water_resistant: number;
  photo: string;
}

interface ClothingSuggestions {
  temperature_range_id: number;
  temperature_range_text: string;
  weather_conditions: Record<string, string>;
  clothing_suggestions: Record<string, ClothingItem[]>;
  message: string;
}

interface CarouselState {
  api: CarouselApi | null;
  current: number;
  total: number;
}

// Define props
const props = defineProps<{
  weather: any;
}>()

// Reactive variables
const suggestions = ref<ClothingSuggestions | null>(null)
const isLoading = ref(false)
const error = ref<string | null>(null)
const carouselStates = reactive<Record<string, CarouselState>>({})

// Fetch clothing suggestions when weather prop changes
watch(() => props.weather, async (newWeather) => {
  if (newWeather) {
    await fetchClothingSuggestions(newWeather)
  }
})

// Function to fetch clothing suggestions from the API
const fetchClothingSuggestions = async (weatherData: any) => {
  isLoading.value = true
  error.value = null
  try {
    const response = await api.post<ClothingSuggestions>('/api/clothing-suggestions', { weather: weatherData })
    suggestions.value = response.data
  } catch (err: any) {
    console.error('Error fetching clothing suggestions:', err)
    error.value = 'Failed to fetch clothing suggestions. Please try again.'
  } finally {
    isLoading.value = false
  }
}

// Function to set up carousel API for each category
const setApi = (category: string, api: CarouselApi) => {
  if (!carouselStates[category]) {
    carouselStates[category] = {
      api: null,
      current: 1,
      total: 0,
    }
  }

  carouselStates[category].api = api
  carouselStates[category].total = api.scrollSnapList().length
  carouselStates[category].current = api.selectedScrollSnap() + 1

  api.on('select', () => {
    carouselStates[category].current = api.selectedScrollSnap() + 1
  })
}

// Function to get the appropriate layer icon based on category
const getLayerIcon = (category: string) => {
  return Layers
}

// Fetch clothing suggestions on component mount
onMounted(() => {
  if (props.weather) {
    fetchClothingSuggestions(props.weather)
  }
})
</script>