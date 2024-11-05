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
import {FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage} from "@/components/ui/form";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

const { toast } = useToast()
const authStore = useAuthStore()

const formSchema = toTypedSchema(z.object({
  email: z.string().email().toLowerCase(),
  password: z.string().min(6),
  password_confirmation: z.string().min(6),
  gender: z.enum(['Male', 'Female'], {
    required_error: 'Please select your gender',
  }),
}).refine(data => data.password === data.password_confirmation, {
  message: "Passwords don't match",
  path: ["password_confirmation"],
}));

const { handleSubmit } = useForm({
  validationSchema: formSchema,
});

const onSubmit = handleSubmit(async (formValues) => {
  try {
    const response = await api.post('/api/register', formValues);
    authStore.setUser(response.data.user);
    authStore.setToken(response.data.token);
    toast({
      title: 'Registration successful',
      description: 'Please check your email to verify your account.',
    });
    router.push('/verify-email');
  } catch (error) {
    toast({
      title: 'Registration failed',
      description: error.response?.data?.message || 'Registration failed',
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
            Sign Up
          </h1>
          <p class="text-balance text-muted-foreground">
            Enter your information to create an account
          </p>
        </div>

        <form class="space-y-4" @submit="onSubmit">

          <FormField v-slot="{ componentField }" name="email">
            <FormItem>
              <FormLabel>Email</FormLabel>
              <FormControl>
                <Input type="email" placeholder="yourname@example.com" v-bind="componentField" />
              </FormControl>
              <FormDescription>
                This is your email address.
              </FormDescription>
              <FormMessage />
            </FormItem>
          </FormField>

          <FormField v-slot="{ componentField }" name="password">
            <FormItem>
              <FormLabel>Password</FormLabel>
              <FormControl>
                <Input type="password" placeholder="********" v-bind="componentField" />
              </FormControl>
              <FormDescription>
                Your password must be at least 6 characters long.
              </FormDescription>
              <FormMessage />
            </FormItem>
          </FormField>

          <FormField v-slot="{ componentField }" name="password_confirmation">
            <FormItem>
              <FormLabel>Confirm Password</FormLabel>
              <FormControl>
                <Input type="password" placeholder="********" v-bind="componentField" />
              </FormControl>
              <FormDescription>
                Please re-enter your password.
              </FormDescription>
              <FormMessage />
            </FormItem>
          </FormField>

          <!-- Gender Select Field -->
          <FormField v-slot="{ componentField }" name="gender">
            <FormItem>
              <FormLabel>Gender</FormLabel>
              <FormControl>
                <Select v-bind="componentField">
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select your gender" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectLabel>Gender</SelectLabel>
                      <SelectItem value="Male">
                        Male
                      </SelectItem>
                      <SelectItem value="Female">
                        Female
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
              </FormControl>
              <FormDescription>
                Please select your gender.
              </FormDescription>
              <FormMessage />
            </FormItem>
          </FormField>

          <Button class="w-full" type="submit">
            Submit
          </Button>
        </form>

        <div class="mt-4 text-center text-sm">
          Already have an account?
          <router-link to="/login" class="underline">
            Sign in
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>
