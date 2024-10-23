<script setup lang="ts">

import ProfileLayout from "@/layouts/ProfileLayout.vue";
import ProfileForm from "@/components/profile/ProfileForm.vue";
import NavBar from "@/components/NavBar.vue";
</script>

<template>
  <NavBar></NavBar>
  <ProfileLayout>
    <ProfileForm />
  </ProfileLayout>
</template>