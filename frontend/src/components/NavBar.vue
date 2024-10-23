<script setup>
import {ref, computed, onMounted} from 'vue'

const role = ref(null)

// Check for user role in local storage
onMounted(() => {
  role.value = localStorage.getItem('role') || 'user'
})

const isStylist = computed(() => role.value === 'stylist')
</script>

<template>
  <!-- Navbar is only visible if user is authenticated -->
  <nav class="bg-white border-gray-200 dark:bg-gray-900 shadow-lg">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
      <!-- Brand and Logo -->
      <a href="#" class="flex items-center space-x-3">
        <img src="/falla.jpg" class="h-8" alt="F" />
        <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Falla</span>
      </a>
      <!-- Mobile toggle button -->
      <button data-collapse-toggle="navbar-default" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-default" aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15" />
        </svg>
      </button>
      <!-- Links -->
      <div class="hidden w-full md:block md:w-auto" id="navbar-default">
        <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700 items-center">
          <li>
            <a href="/home" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">
              Home
            </a>
          </li>
          <li>
            <a href="/wardrobe" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white">
              Wardrobe
            </a>
          </li>
          <!-- Stylists Page (only visible for stylists) -->
          <li v-if="isStylist">
            <a href="/stylist-panel" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white">
              Stylists Page
            </a>
          </li>
          <li>
            <a href="/profile" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white">
              Profile
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<style scoped>
nav {
  background-color: #f8f9fa;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

a {
  font-weight: 500;
  transition: all 0.2s ease;
}

a:hover {
  color: #4a90e2;
}

a.active {
  background-color: #1d4ed8;
  color: white;
  border-radius: 4px;
}

img {
  height: 40px;
}
</style>
