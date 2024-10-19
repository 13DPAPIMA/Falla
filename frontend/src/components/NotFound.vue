<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-orange-200 to-amber-600">
    <div class="text-center">
      <Leaf class="mx-auto h-24 w-24 text-amber-800 mb-6" />
      <h1 class="text-4xl font-bold text-amber-900 mb-2">404 - Page Not Found</h1>
      <p class="text-xl text-amber-800 mb-8">The page you're looking for has fallen out of our tree.</p>
      <Button @click="goHome" variant="secondary" size="lg" class="font-semibold">
        <Home class="mr-2 h-5 w-5" />
        Return to Home
      </Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Leaf, Home } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { useRouter } from 'vue-router'

const router = useRouter()

const goHome = () => {
  router.push('/home')
}
</script>