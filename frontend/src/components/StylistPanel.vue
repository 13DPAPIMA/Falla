<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/api'
import HeaderNavBar from "@/components/HeaderNavbar.vue"
import { Button } from "@/components/ui/button"
import { Textarea } from "@/components/ui/textarea"
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from "@/components/ui/card"
import { toast } from "@/components/ui/toast"

interface User {
  id: number
  email: string
  role: string
  gender: string
}

interface Question {
  id: number
  user_id: number
  topic: string
  description: string
  created_at: string
  updated_at: string
  user: User
  answer: string
}

const authStore = useAuthStore()
const questions = ref<Question[]>([])
const isLoading = ref(false)

const fetchQuestions = async () => {
  isLoading.value = true
  try {
    const response = await api.get('/api/questions')
    questions.value = response.data.map((q: Question) => ({ ...q, answer: '' }))
  } catch (error) {
    console.error('Error fetching questions:', error)
    toast({
      title: "Error",
      description: "Failed to fetch questions. Please try again.",
      variant: "destructive",
    })
  } finally {
    isLoading.value = false
  }
}

const submitAnswer = async (questionId: number, answer: string) => {
  if (!answer.trim()) {
    toast({
      title: "Error",
      description: "Please enter an answer before submitting.",
      variant: "destructive",
    })
    return
  }

  try {
    await api.post('/api/answers', {
      question_id: questionId,
      content: answer.trim()
    })
    toast({
      title: "Success",
      description: "Answer submitted successfully.",
    })
    await fetchQuestions()
  } catch (error) {
    console.error('Error submitting answer:', error)
    toast({
      title: "Error",
      description: "Failed to submit answer. Please try again.",
      variant: "destructive",
    })
  }
}

onMounted(async () => {
  const isAuthenticated = await authStore.checkAuth()
  if (isAuthenticated) {
    fetchQuestions()
  } else {
    // Redirect to login page or show a message
    toast({
      title: "Authentication Required",
      description: "Please log in to view and answer questions.",
      variant: "destructive",
    })
  }
})
</script>

<template>
  <div class="min-h-screen bg-background">
    <HeaderNavBar />
    <main class="container mx-auto p-4 space-y-6">
      <h1 class="text-3xl font-bold">Answer Questions</h1>
      <div v-if="isLoading" class="text-center">
        <p>Loading questions...</p>
      </div>
      <div v-else-if="questions.length === 0" class="text-center">
        <p>No questions available at the moment.</p>
      </div>
      <div v-else class="space-y-4">
        <Card v-for="question in questions" :key="question.id" class="w-full">
          <CardHeader>
            <CardTitle>Question from {{ question.user.email }}</CardTitle>
          </CardHeader>
          <CardContent>
            <h3 class="font-semibold">{{ question.topic }}</h3>
            <p class="mt-2">{{ question.description }}</p>
            <div class="mt-2 text-sm text-gray-500">
              Asked on: {{ new Date(question.created_at).toLocaleString() }}
            </div>
            <Textarea
                v-model="question.answer"
                placeholder="Type your answer here..."
                class="mt-4"
            />
          </CardContent>
          <CardFooter>
            <Button @click="submitAnswer(question.id, question.answer)">
              Submit Answer
            </Button>
          </CardFooter>
        </Card>
      </div>
    </main>
  </div>
</template>