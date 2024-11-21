<template>
  <Card class="overflow-hidden transition-all duration-300 hover:shadow-lg relative group">
    <CardHeader>
      <CardTitle class="flex items-center justify-between">
        {{ item.type.type }}
        <Badge variant="outline">{{ item.style.style }}</Badge>
      </CardTitle>
      <CardDescription>{{ item.material.material }}</CardDescription>
    </CardHeader>
    <CardContent>
      <div class="relative">
        <img
            :src="item.photo?.photo_url || '/placeholder.svg?height=200&width=200'"
            :alt="item.type.type"
            class="w-full h-48 object-cover mb-4"
        />
        <div v-if="!isInWardrobe" class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
          <Button size="icon" variant="secondary" class="rounded-full bg-green-500 hover:bg-green-600 w-12 h-12" @click="$emit('add', item.id)">
            <Plus class="h-6 w-6" />
          </Button>
        </div>
      </div>
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
    <CardFooter v-if="isInWardrobe">
      <AlertDialog>
        <AlertDialogTrigger asChild>
          <Button variant="destructive" class="w-full">
            <Trash class="w-4 h-4 mr-2" />
            Remove from Wardrobe
          </Button>
        </AlertDialogTrigger>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Are you sure?</AlertDialogTitle>
            <AlertDialogDescription>
              This action cannot be undone. This will permanently remove the item from your wardrobe.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
                class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                @click="$emit('remove', item.id)"
            >
              Remove
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </CardFooter>
  </Card>
</template>

<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { AlertDialog, AlertDialogTrigger, AlertDialogContent, AlertDialogHeader, AlertDialogTitle, AlertDialogDescription, AlertDialogFooter, AlertDialogCancel, AlertDialogAction } from '@/components/ui/alert-dialog'
import { Droplet, Plus, Trash } from 'lucide-vue-next'

interface ClothingItem {
  id: number
  type: { type: string }
  style: { style: string }
  material: { material: string }
  color: string
  water_resistant: boolean
  photo?: { photo_url: string }
}

defineProps<{
  item: ClothingItem
  isInWardrobe: boolean
}>()

defineEmits(['add', 'remove'])

function capitalizeFirstLetter(string: string) {
  return string.charAt(0).toUpperCase() + string.slice(1)
}
</script>