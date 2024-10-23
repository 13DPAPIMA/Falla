<template>
  <div class="min-h-screen p-6">

    <NavBar></NavBar>

    <main class="max-w-3xl mx-auto p-6">
      <div class="mb-6">
        <div class="flex space-x-4">
          <Input v-model="city" type="text" placeholder="Enter city" class="flex-grow" />
          <Button @click="fetchWeather" :disabled="isLoading">
            <Loader v-if="isLoading" class="mr-2 h-4 w-4 animate-spin" />
            Get Weather
          </Button>
        </div>
        <p v-if="errorMessage" class="mt-2 text-red-600">{{ errorMessage }}</p>
      </div>

      <div v-if="weather" class="space-y-8">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-semibold text-gray-800">{{ weather.city.name }}, {{ weather.city.country }}</h2>
            <p class="text-gray-600">{{ formatDate(todayForecast[0].dt_txt) }}</p>
          </div>
          <div class="text-right">
            <p class="text-4xl font-bold text-gray-800">{{ Math.round(todayForecast[0].main.temp) }}°C</p>
            <p class="text-gray-600">Feels like {{ Math.round(todayForecast[0].main.feels_like) }}°C</p>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-4 text-center">
          <div>
            <CloudRainWind class="mx-auto h-8 w-8 text-blue-500" />
            <p class="mt-1 font-medium">{{ todayForecast[0].main.humidity }}%</p>
            <p class="text-sm text-gray-600">Humidity</p>
          </div>
          <div>
            <Wind class="mx-auto h-8 w-8 text-blue-500" />
            <p class="mt-1 font-medium">{{ todayForecast[0].wind.speed }} m/s</p>
            <p class="text-sm text-gray-600">Wind Speed</p>
          </div>
          <div>
            <Thermometer class="mx-auto h-8 w-8 text-blue-500" />
            <p class="mt-1 font-medium">{{ Math.round(todayForecast[0].main.feels_like) }}°C</p>
            <p class="text-sm text-gray-600">Feels Like</p>
          </div>
        </div>
      </div>

      <div v-else-if="!isLoading" class="text-center text-gray-600">
        <Cloud class="mx-auto h-16 w-16 text-gray-400" />
        <p class="mt-2">Enter a city to get weather information</p>
      </div>

      <Separator class="my-6" />

      <ClothingSuggestions v-if="weather" :weather="weather" />
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import api from '@/api'
import { Separator } from '@/components/ui/separator'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import ClothingSuggestions from './ClothingSuggestions.vue'
import { Cloud, CloudRainWind, Wind, Thermometer, Loader } from 'lucide-vue-next'

import NavBar from '@/components/NavBar.vue';

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

onMounted(() => {
  fetchWeather()
})
</script>

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
