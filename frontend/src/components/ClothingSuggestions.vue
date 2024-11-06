<template>
  <div class="clothing-suggestions">
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
        <div class="space-y-8">
          <div v-for="(categoryItems, category) in suggestions.clothing_suggestions" :key="category">
            <div v-if="categoryItems && categoryItems.length > 0">
              <h3 class="flex items-center space-x-2 text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">
                <component :is="getLayerIcon(category)" class="h-6 w-6" />
                <span class="capitalize">{{ category }}</span>
              </h3>
              <Carousel class="w-full max-w-sm mx-auto" @init-api="(api) => setApi(category, api)">
                <CarouselContent>
                  <CarouselItem v-for="(item, index) in categoryItems" :key="index" class="basis-full">
                    <div class="p-1">
                      <Card class="overflow-hidden transition-all duration-300 hover:shadow-lg bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                        <CardHeader class="pb-2">
                          <CardTitle class="text-lg font-bold">{{ item.style }} {{ item.type }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                          <div class="aspect-square relative mb-4 bg-gray-200 dark:bg-gray-700 rounded-md overflow-hidden flex items-center justify-center">
                            <img v-if="item.photo" :src="item.photo" :alt="item.type" class="w-full h-full object-cover" />
                            <div v-else class="text-4xl font-bold text-gray-400 dark:text-gray-600">
                              {{ item.type.charAt(0).toUpperCase() }}
                            </div>
                          </div>
                          
                          <div class="grid grid-cols-2 gap-3">
                            <div class="flex items-center space-x-2">
                              <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Material:</span>
                              <span class="text-sm text-gray-800 dark:text-gray-200">{{ item.material }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                              <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Color:</span>
                              <div class="flex items-center">
                                <div
                                    class="w-4 h-4 rounded-full mr-1 border border-gray-300 dark:border-gray-600"
                                    :style="{ backgroundColor: item.color }"
                                ></div>
                                <span class="text-sm capitalize text-gray-800 dark:text-gray-200">{{ item.color }}</span>
                              </div>
                            </div>
                            <div v-if="item.water_resistant === 1" class="flex items-center space-x-2">
                              <Droplet class="h-4 w-4 text-blue-500" />
                              <span class="text-sm text-blue-500">Water Resistant</span>
                            </div>
                          </div>
                        </CardContent>
                      </Card>
                    </div>
                  </CarouselItem>
                </CarouselContent>
                <CarouselPrevious />
                <CarouselNext />
              </Carousel>
              <div class="py-2 text-center text-sm text-muted-foreground">
                Slide {{ carouselStates[category]?.current || 1 }} of {{ carouselStates[category]?.total || categoryItems.length }}
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
import { Badge } from '@/components/ui/badge'
import { CircleAlert, Loader, Droplet, Shirt, Layers } from 'lucide-vue-next'
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel'
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
  switch (category.toLowerCase()) {
    case 'base':
      return Layers
    case 'mid':
      return Layers
    case 'outer':
      return Layers
    case 'pants':
      return Layers
    default:
      return Layers
  }
}

// Fetch clothing suggestions on component mount
onMounted(() => {
  if (props.weather) {
    fetchClothingSuggestions(props.weather)
  }
})
</script>