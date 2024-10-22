<template>
  <Card class="overflow-hidden transition-all duration-300 hover:shadow-lg">
    <CardHeader>
      <CardTitle class="flex items-center justify-between">
        {{ item.type.type }}
        <Badge variant="outline">{{ item.style.style }}</Badge>
      </CardTitle>
      <CardDescription>{{ item.color }}</CardDescription>
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
      <Button
          v-if="isAvailable"
          @click="$emit('add', item.id)"
          class="w-full"
          variant="default"
      >
        <Plus class="w-4 h-4 mr-2" />
        Add to Wardrobe
      </Button>
      <AlertDialog v-else>
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

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  isAvailable: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['add', 'remove'])

function capitalizeFirstLetter(string: string) {
  return string.charAt(0).toUpperCase() + string.slice(1)
}
</script>