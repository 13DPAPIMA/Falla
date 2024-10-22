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
      <div v-if="typeof suggestions.clothing_suggestions === 'object'">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
          <Card v-for="(items, category) in suggestions.clothing_suggestions" :key="category">
            <CardHeader>
              <CardTitle>{{ category }}</CardTitle>
            </CardHeader>
            <CardContent>
              <ul class="space-y-2">
                <li v-for="(item, index) in items.slice(0, 3)" :key="index" class="flex items-center space-x-2">
                  <span>{{ item.style }} {{ item.material }}</span>
                </li>
              </ul>
            </CardContent>
          </Card>
        </div>
      </div>
      <div v-else class="mt-16 text-center text-destructive">
        {{ suggestions.clothing_suggestions || suggestions.message }}
      </div>
    </div>
    <div v-else class="mt-16 text-center text-destructive">
      No clothing suggestions available.
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import api from '@/api'
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { CircleAlert, Loader } from 'lucide-vue-next'

// Interfaces for TypeScript
interface ClothingItem {
  type: string;
  style: string;
  material: string;
  photo: { photo_url: string } | null;
}

interface ClothingSuggestions {
  temperature_range_id: number;
  temperature_range_text: string;
  weather_conditions: string[];
  clothing_suggestions: Record<string, ClothingItem[]> | string;
  message?: string;
}

// Define props
const props = defineProps<{
  weather: any;
}>()

// Reactive variables
const suggestions = ref<ClothingSuggestions | null>(null)
const isLoading = ref(false)
const error = ref<string | null>(null)

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

// Fetch clothing suggestions on component mount
onMounted(() => {
  if (props.weather) {
    fetchClothingSuggestions(props.weather);
  }
})
</script>