<script setup lang="ts">

import ProfileLayout from "@/layouts/ProfileLayout.vue";
import ProfileForm from "@/components/profile/ProfileForm.vue";
import HeaderNavBar from "@/components/HeaderNavbar.vue";
</script>

<template>
  <HeaderNavBar></HeaderNavBar>

  <ProfileLayout>
    <ProfileForm />
  </ProfileLayout>
</template>