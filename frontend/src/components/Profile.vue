<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useToast } from '@/components/ui/toast/use-toast';
import api from '@/api.ts';
import router from '@/router.ts';
import { Button } from "@/components/ui/button";

const { toast } = useToast();

const userData = ref<Record<string, any> | null>(null);
const updatedEmail = ref<string>('');
const updatedGender = ref<string>('');
const updatedPassword = ref<string>('');  // Password field
const passwordConfirmation = ref<string>('');  // Password confirmation field

// Fetch user data when the component mounts
const fetchUserData = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      router.push('api/login');
      return; // Exit function early if token is not available
    }

    const response = await api.get('api/user', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    userData.value = response.data;
    updatedEmail.value = userData.value.email;
    updatedGender.value = userData.value.gender;

  } catch (error) {
    console.error(error); // Log error for debugging
    toast({
      title: 'Error fetching user data',
      description: 'Unable to load user data. Please try again later.',
    });
  }
};

// Logout function
const logout = async () => {
  try {
    const token = localStorage.getItem('token');

    await api.post('api/logout', {}, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    localStorage.removeItem('token');
    router.push('/');

    toast({
      title: 'Logged out',
      description: 'You have been successfully logged out.',
    });
  } catch (error) {
    console.error(error); // Log error for debugging
    toast({
      title: 'Error logging out',
      description: 'Unable to log out. Please try again later.',
    });
  }
};

// Update user data function
const updateUserData = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      router.push('api/login');
      return;
    }

    // Check if passwords match before sending the request
    if (updatedPassword.value && updatedPassword.value !== passwordConfirmation.value) {
      toast({
        title: 'Error',
        description: 'Passwords do not match. Please try again.',
      });
      return;
    }

    const response = await api.put('api/user', {
      email: updatedEmail.value,
      gender: updatedGender.value,
      password: updatedPassword.value,  // Include password in the request
      password_confirmation: passwordConfirmation.value,  // Include password confirmation in the request
    }, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    userData.value = response.data;

    toast({
      title: 'Profile updated',
      description: 'Your profile has been successfully updated.',
    });

  } catch (error) {
    console.error(error.response.data); 
    if (error.response && error.response.data && error.response.data.errors) {
      const errorMessages = Object.values(error.response.data.errors).flat().join(' ');
      toast({
        title: 'Error updating profile',
        description: errorMessages,
      });
    } else {
      toast({
        title: 'Error updating profile',
        description: 'Failed to update your profile. Please try again later.',
      });
    }
  }
};


onMounted(async () => {
  await fetchUserData();
});
</script>

<template>
  <div v-if="userData" class="text-center">
    <div class="my-10 bg-white shadow-xl rounded-xl p-6 mt-10 mx-auto max-w-lg">
      <h1 class="text-3xl font-bold mb-4">Profile</h1>
      <p class="text-gray-500 mb-6">On this page, your profile data is displayed</p>
      <hr />

      <!-- Email Input -->
      <div class="my-3 bg-gray-100 p-3">
        <label for="email">Your Email</label>
        <input 
          id="email"
          v-model="updatedEmail"
          type="email"
          class="block w-full mt-2 border-gray-300 rounded-md p-2"
        />
      </div>

      <!-- Gender Dropdown -->
      <div class="bg-gray-100 p-3 mt-4">
        <label for="gender">Your Gender</label>
        <select 
          id="gender" 
          v-model="updatedGender"
          class="block w-full mt-2 border-gray-300 rounded-md p-2"
        >
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>

      <!-- Password Input -->
      <div class="my-3 bg-gray-100 p-3">
        <label for="password">New Password</label>
        <input 
          id="password"
          v-model="updatedPassword"
          type="password"
          class="block w-full mt-2 border-gray-300 rounded-md p-2"
        />
      </div>

      <!-- Password Confirmation Input -->
      <div class="my-3 bg-gray-100 p-3">
        <label for="password-confirm">Confirm Password</label>
        <input 
          id="password-confirm"
          v-model="passwordConfirmation"
          type="password"
          class="block w-full mt-2 border-gray-300 rounded-md p-2"
        />
      </div>

      <!-- Update Button -->
      <div>
        <Button class="mt-6" @click="updateUserData">Update Profile</Button>
      </div>

      <!-- Logout Button -->
      <div>
        <Button variant="destructive" class="mt-6" @click="logout">Logout</Button>
      </div>
    </div>
  </div>

  <div v-else class="text-center mt-20">
    <p>Loading user data...</p>
  </div>
</template>
