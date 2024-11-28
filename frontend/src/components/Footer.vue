<script setup lang="ts">
import { computed } from 'vue'
import { HelpCircle } from 'lucide-vue-next'

const currentYear = computed(() => new Date().getFullYear())

const userManualUrl = '/Falla-user-manual.pdf'
</script>

<template>
  <div class="mt-[-60px]">
    <footer class="py-6 bg-background">
      <div class="container mx-auto px-4">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
          <p class="text-sm text-muted-foreground">
            &copy; {{ currentYear }} Your School Project Name
          </p>
          <a :href="userManualUrl" target="_blank" class="flex items-center text-muted-foreground justify-center">
            <span class="mr-2">Having trouble?</span>
            <HelpCircle class="h-4 w-4" />
          </a>
        </div>
      </div>
    </footer>
  </div>
</template>