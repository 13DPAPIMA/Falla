<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useToast } from '@/components/ui/toast/use-toast'
import api from '@/api.ts';
import router from '@/router.ts';
import { Button } from "@/components/ui/button"

const { toast } = useToast()

const userData = ref<Record<string, any> | null>(null);

const fetchUserData = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      router.push('/login');
      return; // Exit function early if token is not available
    }

    const response = await api.get('/api/user', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    userData.value = response.data;

  } catch (error) {
    toast({
      title: 'Error fetching user data',
      description: 'Unable to load user data. Please try again later.',
    });
  }
};

const logout = async () => {
  const token = localStorage.getItem('token');

  await api.post('/api/logout', {}, {
    headers: {
      Authorization: `Bearer ${token}`
    }
  });

  localStorage.removeItem('token');
  router.push('/');

  toast({
    title: 'Logged out',
    description: 'You have been successfully logged out.',
  });
};

onMounted(async () => {
  await fetchUserData();
});
</script>


<template>
  <div v-if="userData">
    <h1>User Profile</h1>
    <p><strong>Email:</strong> {{ userData.email }}</p>

    <Button @click="logout">Logout</Button>
  </div>
  <div v-else>
    <p>Loading user data...</p>
  </div>
</template>
