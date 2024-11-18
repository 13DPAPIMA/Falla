<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import api from '@/api'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import ClothingSuggestions from './ClothingSuggestions.vue'
import { Cloud, CloudRainWind, Wind, Thermometer, Loader, ChevronDown } from 'lucide-vue-next'
import HeaderNavBar from '@/components/HeaderNavBar.vue'
import AskExpert from '@/components/AskExpert.vue'

interface WeatherData {
  city: {
    name: string
    country: string
  }
  list: Array<{
    dt_txt: string
    main: {
      temp: number
      feels_like: number
      humidity: number
    }
    weather: Array<{
      description: string
      icon: string
    }>
    wind: {
      speed: number
    }
  }>
}

const weather = ref<WeatherData | null>(null)
const city = ref('Riga')
const errorMessage = ref<string | null>(null)
const isLoading = ref(false)

const todayForecast = computed(() => {
  if (!weather.value || !weather.value.list) return []

  const today = new Date().toISOString().split('T')[0]
  return weather.value.list.filter((forecast) => {
    const forecastDate = forecast.dt_txt.split(' ')[0]
    return forecastDate === today
  })
})

const formatDate = (dateTime: string) => {
  const options: Intl.DateTimeFormatOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(dateTime).toLocaleDateString(undefined, options)
}

const fetchWeather = async () => {
  const cityToFetch = city.value || 'Riga'
  isLoading.value = true
  errorMessage.value = null

  try {
    const response = await api.get<WeatherData>(`/api/weather?city=${encodeURIComponent(cityToFetch)}`)
    weather.value = response.data
  } catch (error: any) {
    console.error('Error fetching weather data:', error)

    if (api.isAxiosError(error)) {
      if (error.response) {
        errorMessage.value = `Error ${error.response.status}: ${error.response.statusText}`
      } else if (error.request) {
        errorMessage.value = 'No response received from server.'
      } else {
        errorMessage.value = `Error: ${error.message}`
      }
    } else {
      errorMessage.value = 'An unexpected error occurred.'
    }

    weather.value = null
  } finally {
    isLoading.value = false
  }
}

const scrollToSuggestions = () => {
  const suggestionsElement = document.getElementById('clothing-suggestions')
  if (suggestionsElement) {
    suggestionsElement.scrollIntoView({ behavior: 'smooth' })
  }
}

onMounted(() => {
  fetchWeather()
})
</script>

<template>
  <div class="min-h-screen">
    <HeaderNavBar></HeaderNavBar>

    <main>
      <section class="min-h-screen flex flex-col justify-center px-6">
        <div class="max-w-4xl mx-auto w-full">
          <div class="mb-12">
            <div class="flex space-x-4">
              <Input v-model="city" type="text" placeholder="Enter city" class="flex-grow text-lg" />
              <Button @click="fetchWeather" :disabled="isLoading" class="text-lg py-3">
                <Loader v-if="isLoading" class="mr-2 h-5 w-5 animate-spin" />
                Get Weather
              </Button>
            </div>
            <p v-if="errorMessage" class="mt-4 text-red-600 text-lg">{{ errorMessage }}</p>
          </div>

          <div v-if="weather" class="space-y-12">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ weather.city.name }}, {{ weather.city.country }}</h1>
              <p class="text-l text-gray-600 mb-8">{{ formatDate(todayForecast[0].dt_txt) }}</p>

              <div class="flex items-center justify-between mb-12">
                <div class="text-5xl font-bold text-gray-900">{{ Math.round(todayForecast[0].main.temp) }}°C</div>
                <div class="text-l text-gray-600">Feels like {{ Math.round(todayForecast[0].main.feels_like) }}°C</div>
              </div>

              <div class="grid grid-cols-3 gap-8 text-center">
                <div>
                  <CloudRainWind class="mx-auto h-16 w-16 text-blue-500 mb-4" />
                  <p class="text-l font-medium text-gray-900">{{ todayForecast[0].main.humidity }}%</p>
                  <p class="text-gray-600">Humidity</p>
                </div>
                <div>
                  <Wind class="mx-auto h-16 w-16 text-blue-500 mb-4" />
                  <p class="text-l font-medium text-gray-900">{{ todayForecast[0].wind.speed }} m/s</p>
                  <p class="text-gray-600">Wind Speed</p>
                </div>
                <div>
                  <Thermometer class="mx-auto h-16 w-16 text-blue-500 mb-4" />
                  <p class="text-l font-medium text-gray-900">{{ Math.round(todayForecast[0].main.feels_like) }}°C</p>
                  <p class="text-gray-600">Feels Like</p>
                </div>
              </div>
            </div>
          </div>

          <div v-else-if="!isLoading" class="text-center text-gray-600">
            <Cloud class="mx-auto h-32 w-32 text-gray-400 mb-6" />
            <p class="text-2xl">Enter a city to get weather information</p>
          </div>

          <div v-if="weather" class="mt-16 text-center">
            <Button @click="scrollToSuggestions" variant="ghost" class="text-lg">
              See Clothing Suggestions
              <ChevronDown class="ml-2 h-6 w-6" />
            </Button>
          </div>
        </div>
      </section>

      <section id="clothing-suggestions" class="py-24 px-6">
        <div class="max-w-4xl mx-auto">
          <ClothingSuggestions v-if="weather" :weather="weather" />
        </div>
      </section>

      <section class="py-24 px-6">
        <div class="max-w-4xl mx-auto">
          <AskExpert />
        </div>
      </section>
    </main>
  </div>
</template>

<style scoped>
img {
  margin-top: 20px;
  max-width: 300px;
  border: 1px solid #ccc;
}

.clothing-item img {
  width: 150px;
  height: auto;
}
</style>