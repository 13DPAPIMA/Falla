<script setup>
import { ref, onMounted } from 'vue'
import HeaderNavBar from "@/components/HeaderNavbar.vue";
// Placeholder for questions fetched from the backend
const questions = ref([
  { id: 1, user: 'User1', question: 'What should I wear to a wedding?', answer: '' },
  { id: 2, user: 'User2', question: 'What color would suit me?', answer: '' },
  { id: 3, user: 'User3', question: 'Is this jacket good for cold weather?', answer: '' }
]);
// Answer submission function
const submitAnswer = (questionId, answer) => {
  const questionIndex = questions.value.findIndex(q => q.id === questionId);
  if (questionIndex !== -1) {
    // Update the answer in the local state
    questions.value[questionIndex].answer = answer;
    // Here you can send the answer to your backend API
    // Example:
    // await fetch('/api/answer', {
    //   method: 'POST',
    //   headers: { 'Content-Type': 'application/json' },
    //   body: JSON.stringify({ id: questionId, answer })
    // });

    alert(`Answer submitted for question ${questionId}: ${answer}`);
  }
};
</script>

<template>
  <HeaderNavBar></HeaderNavBar>

  <div id="main" class="h-screen flex flex-col justify-start items-start p-8">
    <!-- Display each question from the users -->
    <div v-for="question in questions" :key="question.id" class="mb-6 w-full border-b pb-4">
      <h3 class="font-semibold">{{ question.user }}'s question:</h3>
      <p>{{ question.question }}</p>

      <!-- Textarea for stylist to provide answer -->
      <textarea v-model="question.answer"
                class="min-h-[100px] min-w-[350px] border-black border-2 rounded text-top mt-2">
      </textarea>

      <!-- Button to submit the answer -->
      <button @click="submitAnswer(question.id, question.answer)"
              class="w-[75px] h-[35px] bg-zinc-500 text-white rounded mt-2 hover:bg-zinc-700 transition-colors">
        Reply
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Custom styles if needed */
</style>