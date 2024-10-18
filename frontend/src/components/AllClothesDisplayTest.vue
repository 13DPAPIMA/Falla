<template>
  <div class="min-h-screen bg-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-6">All clothes</h1>
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        <div v-for="item in clothes" :key="item.id" class="bg-white overflow-hidden shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-semibold text-gray-900">{{ item.type.type }}</h2>
              <Shirt class="h-6 w-6 text-gray-500" />
            </div>
            <p class="text-sm text-gray-600 mb-2">Style: {{ item.style.style }}</p>
            <p class="text-sm text-gray-600 mb-2">Material: {{ item.material.material }}</p>
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div
                    class="w-6 h-6 rounded-full mr-2"
                    :style="{ backgroundColor: item.color }"
                    :class="{ 'border': item.color === 'white' }"
                ></div>
                <span class="text-sm capitalize text-gray-700">{{ item.color }}</span>
              </div>
              <div v-if="item.water_resistant" class="flex items-center text-blue-500">
                <Droplet class="h-4 w-4 mr-1" />
                <span class="text-xs">Water Resistant</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Shirt, Droplet } from 'lucide-vue-next'
import router from "@/router"
import api from "@/api"

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
  type: {
    id: number
    type: string
    created_at: string
    updated_at: string
  }
  material: {
    id: number
    material: string
    created_at: string
    updated_at: string
  }
  style: {
    id: number
    style: string
    created_at: string
    updated_at: string
  }
}

const clothes = ref<ClothingItem[]>([])

onMounted(async () => {
  const token = localStorage.getItem('token')
  if (!token) {
    router.push('/login')
    return
  }

  try {
    const response = await api.get<ClothingItem[]>('/api/clothing', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    clothes.value = response.data
  } catch (error) {
    console.error('Error fetching clothes:', error)
    // You might want to add error handling here, such as displaying an error message to the user
  }
})
</script>