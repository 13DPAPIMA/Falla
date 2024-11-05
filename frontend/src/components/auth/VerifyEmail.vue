<script setup lang="ts">
import { ref } from 'vue'
import { useToast } from '@/components/ui/toast/use-toast'
import api from '@/api'
import router from '@/router'
import { useAuthStore } from '@/stores/auth'
import { Button } from "@/components/ui/button"
import { MailIcon } from 'lucide-vue-next'

const { toast } = useToast()
const authStore = useAuthStore()
const loading = ref(false)

const resendVerification = async () => {
  loading.value = true
  try {
    await api.post('/api/email/verification-notification')
    toast({
      title: 'Verification email sent',
      description: 'A new verification link has been sent to your email address.',
    })
  } catch (error) {
    toast({
      title: 'Error',
      description: error.response?.data?.message || 'An error occurred while sending the verification email.',
      variant: 'destructive',
    })
  } finally {
    loading.value = false
  }
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div class="text-center">
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          Verify your email
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </p>
      </div>

      <div class="mt-8 space-y-6">
        <Button
            @click="resendVerification"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span class="absolute left-0 inset-y-0 flex items-center pl-3">
            <MailIcon
                class="h-5 w-5 text-primary-foreground group-hover:text-primary-foreground/90"
                aria-hidden="true"
            />
          </span>
          {{ loading ? 'Sending...' : 'Resend Verification Email' }}
        </Button>

        <div class="text-sm text-center">
          <Button
              @click="handleLogout"
              variant="link"
              class="font-medium text-primary hover:text-primary/90"
          >
            Logout
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>