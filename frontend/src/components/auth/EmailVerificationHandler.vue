<script setup lang="ts">
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from '@/components/ui/toast/use-toast'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const { toast } = useToast()
const authStore = useAuthStore()

const verifyEmail = async (url: string) => {
  try {
    await api.get(url)
    await authStore.checkAuth() // Refresh user data
    toast({
      title: 'Email Verified',
      description: 'Your email has been successfully verified.',
    })
    router.push('/home')
  } catch (error) {
    console.error('Email verification failed:', error)
    toast({
      title: 'Verification Failed',
      description: 'Unable to verify your email. Please try again.',
      variant: 'destructive',
    })
    router.push('/verify-email')
  }
}

onMounted(() => {
  const verificationUrl = route.query.verification_url as string
  if (verificationUrl) {
    verifyEmail(verificationUrl)
  } else {
    toast({
      title: 'Invalid Verification Link',
      description: 'The verification link is invalid or has expired.',
      variant: 'destructive',
    })
    router.push('/verify-email')
  }
})
</script>

<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="text-center">
      <h2 class="text-2xl font-semibold mb-4">Verifying your email...</h2>
      <p class="text-gray-600">Please wait while we confirm your email address.</p>
    </div>
  </div>
</template>