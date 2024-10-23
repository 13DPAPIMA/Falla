<script setup lang="ts">
import Toaster from '@/components/ui/toast/Toaster.vue'

import NavBar from './components/NavBar.vue';
</script>

<template>
  <NavBar></NavBar>
  <router-view />
  <Toaster />
</template>

<style scoped>
</style>
