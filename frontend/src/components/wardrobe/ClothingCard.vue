<template>
  <Card class="relative group">
    <CardContent class="p-0">
      <img
          :src="item.photo?.photo_url || '/placeholder.svg?height=200&width=200'"
          :alt="item.type.type"
          class="w-full h-48 object-cover"
      />
      <div class="p-4">
        <h3 class="font-semibold text-lg">{{ item.type.type }}</h3>
        <p class="text-sm text-muted-foreground">{{ item.style.style }} - {{ item.color }}</p>
        <p class="text-sm">{{ item.material.material }}</p>
        <p class="text-sm">{{ item.gender }}</p>
      </div>
    </CardContent>
    <div v-if="!isInWardrobe" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
      <Button size="icon" variant="secondary" class="rounded-full bg-green-500 hover:bg-green-600" @click="$emit('add', item.id)">
        <PlusIcon class="h-4 w-4" />
      </Button>
    </div>
    <div v-else class="absolute bottom-2 right-2">
      <AlertDialog>
        <AlertDialogTrigger asChild>
          <Button variant="destructive" size="sm">Remove</Button>
        </AlertDialogTrigger>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Are you sure?</AlertDialogTitle>
            <AlertDialogDescription>
              This will remove the item from your wardrobe. This action cannot be undone.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction @click="$emit('remove', item.id)">Remove</AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  </Card>
</template>

<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/components/ui/alert-dialog'
import { PlusIcon } from 'lucide-vue-next'

interface ClothingItem {
  id: number
  type: { type: string }
  style: { style: string }
  material: { material: string }
  color: string
  gender: string
  photo?: { photo_url: string }
}

defineProps<{
  item: ClothingItem
  isInWardrobe: boolean
}>()

defineEmits(['add', 'remove'])
</script>