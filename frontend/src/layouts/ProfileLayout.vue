<script setup lang="ts">
import SidebarNav from '@/components/profile/ProfileSideNavBar.vue'
import { Separator } from '@/components/ui/separator'
</script>

<template>
  <div class="hidden space-y-6 p-10 pb-16 md:block">
    <div class="space-y-0.5 flex items-center justify-between">
      <h2 class="text-2xl font-bold tracking-tight">
        Settings
      </h2>
    </div>
    <p class="text-muted-foreground">
      Manage your account settings.
    </p>
    <Separator class="my-6" />
    <div class="flex flex-col space-y-8 lg:flex-row lg:space-y-0">
      <aside class="lg:w-1/6 lg:max-w-xs">
        <SidebarNav />
      </aside>
      <Separator orientation="vertical" class="hidden lg:block mx-6 h-auto" />
      <div class="flex-1 lg:max-w-3xl lg:pl-6">
        <div class="space-y-6">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>