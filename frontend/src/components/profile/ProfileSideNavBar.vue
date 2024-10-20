<script setup lang="ts">
import { useToast } from '@/components/ui/toast/use-toast'
import api from '@/api';
import router from '@/router';
import { Button } from "@/components/ui/button"

const { toast } = useToast()

const logout = async () => {
  const token = localStorage.getItem('token');

  try {
    await api.post('/api/logout', {}, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
  } catch (error) {
    console.error(error);
  }

  localStorage.removeItem('token');
  localStorage.removeItem('role');
  router.push('/');

  toast({
    title: 'Logged out',
    description: 'You have been successfully logged out.',
  });
};
</script>

<template>
  <nav class="flex-col lg:space-y-4">
    <Button variant="ghost" class="w-full text-left justify-start">
      Profile
    </Button>
    <Button variant="destructive" class="w-full text-left justify-start" @click="logout">
      Log out
    </Button>
  </nav>
</template>