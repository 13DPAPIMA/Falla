<script setup lang="ts">
import Wardrobe from '@/components/wardrobe/Wardrobe.vue'
import HeaderNavBar from "@/components/HeaderNavbar.vue";
import Footer from '@/components/Footer.vue'
</script>

<template>
  <div class="flex flex-col">
    <HeaderNavBar />
    <main class="mt-[60px] flex-grow pb-8">
      <Wardrobe />
    </main>
    <Footer/>
  </div>
</template>

