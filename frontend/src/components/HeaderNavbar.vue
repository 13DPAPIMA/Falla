<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { ChevronDown, LogOut, User } from 'lucide-vue-next'

const router = useRouter()
const authStore = useAuthStore()

const isAuthenticated = computed(() => !!authStore.user)
const isStylist = computed(() => authStore.user?.role === 'stylist')

const handleLogout = async () => {
  await authStore.logout()
  router.push('/home')
}

const navigateTo = (route: string) => {
  router.push(route)
}
</script>

<template>
  <header class="bg-background border-b">
    <div class="container mx-auto px-4">
      <nav class="flex items-center justify-between h-16">
        <div class="flex items-center">
          <a href="/" class="text-2xl font-bold text-primary">Falla</a>
        </div>
        <div class="flex items-center space-x-4">
          <template v-if="isAuthenticated">
            <Button variant="ghost" @click="navigateTo('/home')">
              Outfit Recommendations
            </Button>
            <Button variant="ghost" @click="navigateTo('/wardrobe')">
              Wardrobe
            </Button>
            <Button v-if="isStylist" variant="ghost" @click="navigateTo('/stylist-panel')">
              Stylist Panel
            </Button>
            <DropdownMenu>
              <DropdownMenuTrigger as="div">
                <Button variant="ghost" class="flex items-center space-x-1">
                  <User class="w-5 h-5" />
                  <span>Profile</span>
                  <ChevronDown class="w-4 h-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent>
                <DropdownMenuItem @click="navigateTo('/profile')">
                  My Profile
                </DropdownMenuItem>
                <DropdownMenuItem @click="handleLogout">
                  <LogOut class="w-4 h-4 mr-2" />
                  Logout
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </template>
          <template v-else>
            <Button variant="outline" @click="navigateTo('/login')">Login</Button>
            <Button variant="default" @click="navigateTo('/register')">Register</Button>
          </template>
        </div>
      </nav>
    </div>
  </header>
</template>