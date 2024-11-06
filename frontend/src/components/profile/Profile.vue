<script setup lang="ts">

import ProfileForm from "@/components/profile/ProfileForm.vue";
import HeaderNavBar from "@/components/HeaderNavbar.vue";
</script>

<template>
  <div class="min-h-screen">
    <HeaderNavBar />
    <main class="py-4">
      <ProfileForm />
    </main>
  </div>
</template>