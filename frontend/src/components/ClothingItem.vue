<template>
  <Card class="overflow-hidden">
    <CardHeader>
      <div class="flex items-center justify-between">
        <CardTitle>{{ item.type.type }}</CardTitle>
        <component :is="getClothingIcon(item.type.type)" class="h-6 w-6 text-gray-500" />
      </div>
      <CardDescription>{{ item.style.style }}</CardDescription>
    </CardHeader>
    <CardContent>
      <p class="text-sm text-gray-600 mb-2">Material: {{ item.material.material }}</p>
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <div
              class="w-6 h-6 rounded-full mr-2"
              :style="{ backgroundColor: item.color }"
              :class="{ 'border border-gray-300': item.color.toLowerCase() === 'white' }"
          ></div>
          <span class="text-sm capitalize text-gray-700">{{ item.color }}</span>
        </div>
        <div v-if="item.water_resistant" class="flex items-center text-blue-500">
          <Droplet class="h-4 w-4 mr-1" />
          <span class="text-xs">Water Resistant</span>
        </div>
      </div>
    </CardContent>
    <CardFooter>
      <Button v-if="isAvailable" @click="$emit('add', item.id)" class="w-full">
        Add to Wardrobe
      </Button>
      <Button v-else @click="$emit('remove', item.id)" variant="destructive" class="w-full">
        Remove from Wardrobe
      </Button>
    </CardFooter>
  </Card>
</template>

<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Droplet, Shirt, Pants, Jacket, Dress, Sweater, Shorts, Skirt, Umbrella, Hoodie, Tank, Leggings } from 'lucide-vue-next'

interface ClothingItem {
  id: number
  type: { type: string }
  style: { style: string }
  material: { material: string }
  gender: string
  color: string
  water_resistant: number
}

const props = defineProps<{
  item: ClothingItem
  isAvailable: boolean
}>()

const emit = defineEmits(['add', 'remove'])

function getClothingIcon(type: string) {
  const iconMap = {
    'T-Shirt': Shirt,
    'Jeans': Pants,
    'Jacket': Jacket,
    'Dress': Dress,
    'Sweater': Sweater,
    'Shorts': Shorts,
    'Skirt': Skirt,
    'Coat': Umbrella,
    'Hoodie': Hoodie,
    'Tank Top': Tank,
    'Shirt': Shirt,
    'Leggings': Leggings
  }
  return iconMap[type as keyof typeof iconMap] || Shirt
}
</script>