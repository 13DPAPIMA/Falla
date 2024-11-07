<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { MessageCircle, Loader } from 'lucide-vue-next'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const title = ref('')
const question = ref('')
const answer = ref('')
const isLoading = ref(false)
const errorMessage = ref('')
const isAuthenticated = ref(false)

onMounted(async () => {
  isAuthenticated.value = await authStore.checkAuth()
})

const askQuestion = async () => {
  if (!isAuthenticated.value) {
    errorMessage.value = 'Please log in to ask a question.'
    return
  }

  if (!title.value.trim() || !question.value.trim()) {
    errorMessage.value = 'Please enter both a title and a question.'
    return
  }

  isLoading.value = true
  errorMessage.value = ''
  answer.value = ''

  try {
    const response = await api.post('/api/questions', {
      topic: title.value.trim(),
      description: question.value.trim()
    })
    answer.value = response.data.answer
  } catch (error: any) {
    console.error('Error asking question:', error)
    if (api.isAxiosError(error) && error.response) {
      errorMessage.value = `Error: ${error.response.data.message || 'Failed to get an answer'}`
    } else {
      errorMessage.value = 'An unexpected error occurred.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <section class="mt-12">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Ask a stylist</h2>
    <div v-if="isAuthenticated" class="space-y-4">
      <div>
        <label for="question-title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <Input
            id="question-title"
            v-model="title"
            type="text"
            placeholder="Enter the topic of your question"
            class="w-full"
        />
      </div>
      <div>
        <label for="question-description" class="block text-sm font-medium text-gray-700 mb-1">Details</label>
        <Textarea
            id="question-description"
            v-model="question"
            placeholder="Type your weather-related question here..."
            rows="4"
            class="w-full"
        />
      </div>
      <Button @click="askQuestion" :disabled="isLoading" class="w-full py-2">
        <MessageCircle v-if="!isLoading" class="mr-2 h-5 w-5" />
        <Loader v-else class="mr-2 h-5 w-5 animate-spin" />
        {{ isLoading ? 'Asking...' : 'Ask Question' }}
      </Button>
      <p v-if="errorMessage" class="text-red-600">{{ errorMessage }}</p>
      <div v-if="answer" class="mt-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Expert Answer:</h3>
        <p class="text-gray-700 leading-relaxed">{{ answer }}</p>
      </div>
    </div>
    <div v-else class="text-center">
      <p class="text-lg text-gray-700 mb-4">Please log in to ask a question.</p>
      <Button @click="$router.push('/login')" class="py-2">
        Log In
      </Button>
    </div>
  </section>
</template>