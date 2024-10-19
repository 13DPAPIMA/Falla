<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useToast } from '@/components/ui/toast/use-toast';
import { Input } from "@/components/ui/input";
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";

import api from '@/api.ts';
import router from '@/router.ts';
import { Button } from "@/components/ui/button";

const { toast } = useToast();

const userData = ref<Record<string, any> | null>(null);
const updatedEmail = ref<string>('');
const updatedGender = ref<string>('');
const updatedPassword = ref<string>('');  // Password field
const passwordConfirmation = ref<string>('');  // Password confirmation field

// Track which fields are updated
const fieldsUpdated = ref({
  email: false,
  gender: false,
  password: false,
});

// Password visibility control
const isPasswordFieldVisible = ref(false);

// Fetch user data when the component mounts
const fetchUserData = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      router.push('api/login');
      return;
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
    console.error(error); 
    toast({
      title: 'Error fetching user data',
      description: 'Unable to load user data. Please try again later.',
    });
  }
};

// Update user data function (partial updates)
const updateUserData = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      router.push('api/login');
      return;
    }

    const updatedFields: Record<string, any> = {};

    if (fieldsUpdated.value.email) {
      updatedFields.email = updatedEmail.value;
    }
    if (fieldsUpdated.value.gender) {
      updatedFields.gender = updatedGender.value;
    }
    if (fieldsUpdated.value.password) {
      // Check if passwords match before sending the request
      if (updatedPassword.value !== passwordConfirmation.value) {
        toast({
          title: 'Error',
          description: 'Passwords do not match. Please try again.',
        });
        return;
      }
      updatedFields.password = updatedPassword.value;
      updatedFields.password_confirmation = passwordConfirmation.value;
    }

    // If no fields have been updated, prevent submission
    if (Object.keys(updatedFields).length === 0) {
      toast({
        title: 'No changes',
        description: 'No fields have been updated.',
      });
      return;
    }

    const response = await api.put('api/user', updatedFields, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    userData.value = response.data;

    toast({
      title: 'Profile updated',
      description: 'Your profile has been successfully updated.',
    });

    // Reset the tracking object after a successful update
    fieldsUpdated.value = {
      email: false,
      gender: false,
      password: false,
    };

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
  <div v-if="userData" class="my-10 bg-white shadow-xl rounded-xl p-6 mt-10 mx-auto max-w-lg">
    <h1 class="text-3xl font-bold mb-4">Profile</h1>
    <p class="text-gray-500 mb-6">On this page, your profile data is displayed</p>
    <hr />

    <form class="space-y-4" @submit.prevent="updateUserData">  
      <FormField name="email">
        <FormItem>
          <FormLabel>Email</FormLabel>
          <FormControl>
            <Input 
              name="email"
              v-model="updatedEmail"
              @input="fieldsUpdated.email = true"
              type="email"
            />
          </FormControl>
          <FormDescription>This is your email.</FormDescription>
          <FormMessage />
        </FormItem>
      </FormField>

      <!-- Gender Dropdown -->
      <FormField name="gender">
        <FormItem>
          <FormLabel>Gender</FormLabel>
          <FormControl>
            <select v-model="updatedGender" @change="fieldsUpdated.gender = true">
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </FormControl>
          <FormDescription>Select your gender.</FormDescription>
          <FormMessage />
        </FormItem>
      </FormField>

      <Button @click="isPasswordFieldVisible = !isPasswordFieldVisible" type="button">
        {{ isPasswordFieldVisible ? 'Hide Password Fields' : 'Show Password Fields' }}
      </Button>

      <!-- Password Input -->
      <div v-if="isPasswordFieldVisible">
        <FormField name="password">
          <FormItem>
            <FormLabel>New Password</FormLabel>
            <FormControl>
              <Input
                v-model="updatedPassword"
                @input="fieldsUpdated.password = true"
                type="password"
              />
            </FormControl>
            <FormDescription>
              Enter a new password if you want to change it.
            </FormDescription>
            <FormMessage />
          </FormItem>
        </FormField>
      
        <FormField name="passwordConfirmation">
          <FormItem>
            <FormLabel>Confirm Password</FormLabel>
            <FormControl>
              <Input
                v-model="passwordConfirmation"
                @input="fieldsUpdated.password = true"
                type="password"
              />
            </FormControl>
            <FormDescription>
              Confirm your new password.
            </FormDescription>
            <FormMessage />
          </FormItem>
        </FormField>
      </div>

      <!-- Update Button -->
      <div class="flex justify-end">
        <Button class="mt-6" type="submit">Update Profile</Button>
        <Button variant="destructive" class="ml-auto mt-6" @click="logout">Logout</Button>
      </div>
    </form>
  </div>

  <div v-else class="text-center mt-20">
    <p>Loading user data...</p>
  </div>
</template>
