<script setup lang="ts">

import ProfileForm from "@/components/profile/ProfileForm.vue";
import HeaderNavBar from "@/components/HeaderNavbar.vue";
import Footer from '@/components/Footer.vue'
</script>

<template>
  <div class="flex flex-col min-h-screen">
    <HeaderNavBar />
    <main class="mt-[60px] flex-grow py-4">
      <ProfileForm />
    </main>
    <Footer/>
  </div>
</template>