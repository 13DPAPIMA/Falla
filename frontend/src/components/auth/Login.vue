<script setup lang="ts">
import { useForm } from 'vee-validate';
import { useToast } from '@/components/ui/toast/use-toast'
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import api from '@/api';
import router from '@/router';
import { useAuthStore } from '@/stores/auth';

import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import {FormControl, FormField, FormItem, FormLabel, FormMessage} from "@/components/ui/form";
import Footer from '@/components/Footer.vue'

const { toast } = useToast()
const authStore = useAuthStore()

const formSchema = toTypedSchema(z.object({
  email: z.string().email(),
  password: z.string(),
}));

const { handleSubmit } = useForm({
  validationSchema: formSchema,
});

const onSubmit = handleSubmit(async (formValues) => {
  try {
    const response = await api.post('/api/login', formValues);
    authStore.setUser(response.data.user);
    authStore.setToken(response.data.token);
    if (!response.data.user.email_verified_at) {
      router.push('/verify-email');
      toast({ title: 'Verify Email', description: 'Please verify your email before continuing.', variant: 'destructive' });
      return;
    }
    router.push('/home');
  } catch (error) {
    toast({
      title: 'Login failed',
      description: 'Wrong username or password. Please check if the entered data is correct and try again',
      variant: 'destructive',
    });
  }
});
</script>

<template>
  <div class="w-full max-h-screen lg:max-h-[600px] xl:max-h-[800px] overflow-auto">
    <div class="flex items-center justify-center py-12">
      <div class="mx-auto grid w-[350px] gap-6">
        <div class="grid gap-2 text-center">
          <h1 class="text-3xl font-bold">
            Login
          </h1>
          <p class="text-balance text-muted-foreground">
            Enter your email below to login to your account
          </p>
        </div>

        <form class="space-y-4" @submit="onSubmit">

          <FormField v-slot="{ componentField }" name="email">
            <FormItem>
              <FormLabel>Email</FormLabel>
              <FormControl>
                <Input type="email" placeholder="yourname@example.com" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <FormField v-slot="{ componentField }" name="password">
            <FormItem>
              <FormLabel>Password</FormLabel>
              <FormControl>
                <Input type="password" placeholder="********" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <Button class="w-full" type="submit">
            Login
          </Button>
        </form>

        <div class="mt-4 text-center text-sm">
          Don't have an account?
            <router-link to="/register" class="underline">
              Sign up
            </router-link>
        </div>
      </div>
    </div>
  </div>
  <div class="fixed bottom-0 w-full">
    <Footer />
  </div>
</template>
