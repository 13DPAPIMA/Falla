<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'

import { Input } from '@/components/ui/input'
import { FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import { Separator } from '@/components/ui/separator'
import { Button } from '@/components/ui/button'
import { toast } from '@/components/ui/toast'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
import api from '@/api'
import router from '@/router'

const profileFormSchema = toTypedSchema(z.object({
  email: z.string().email({ message: 'Please enter a valid email address.' }).min(8).max(50).optional(),
  currentPassword: z.string().min(1, { message: 'Current password is required.' }).optional(),
  newPassword: z.string().min(8, { message: 'Password must be at least 8 characters.' }).optional(),
  confirmPassword: z.string().optional(),
  gender: z.enum(['Male', 'Female']).optional(),
}))

const { handleSubmit, resetForm, values } = useForm({
  validationSchema: profileFormSchema,
})

const userData = ref({
  email: '',
  gender: '',
})

const isChangingEmail = ref(false)
const isChangingPassword = ref(false)

const fetchUserData = async () => {
  try {
    const response = await api.get('api/user')
    userData.value = response.data
  } catch (error) {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    router.push('/');
    toast({
      title: 'Error fetching user data',
      description: 'Unable to load your profile. Please try again later.',
      variant: 'destructive',
    })
  }
}

const verifyPassword = async (password: string): Promise<boolean> => {
  try {
    const response = await api.post('api/verify-password', { password })
    return response.data.verified
  } catch (error) {
    console.error('Error verifying password:', error)
    return false
  }
}

const onSubmit = handleSubmit(async (formValues) => {
  try {
    const updateData: any = {}

    if ((isChangingEmail.value && formValues.email && formValues.currentPassword) ||
        (isChangingPassword.value && formValues.newPassword && formValues.confirmPassword && formValues.currentPassword)) {
      const isPasswordCorrect = await verifyPassword(formValues.currentPassword)
      if (!isPasswordCorrect) {
        toast({
          title: 'Incorrect Password',
          description: 'The current password you entered is incorrect.',
          variant: 'destructive',
        })
        return
      }
    }

    if (isChangingEmail.value && formValues.email) {
      updateData.email = formValues.email
    }

    if (isChangingPassword.value && formValues.newPassword && formValues.confirmPassword) {
      if (formValues.newPassword !== formValues.confirmPassword) {
        toast({
          title: 'Password mismatch',
          description: 'New password and confirmation do not match.',
          variant: 'destructive',
        })
        return
      }
      updateData.password = formValues.newPassword
      updateData.password_confirmation = formValues.confirmPassword
    }

    if (formValues.gender) {
      updateData.gender = formValues.gender
    }

    if (Object.keys(updateData).length > 0) {
      await api.put('api/user', updateData)

      toast({
        title: 'Profile Updated',
        description: 'Your profile has been successfully updated.',
      })

      isChangingEmail.value = false
      isChangingPassword.value = false
      resetForm()
      await fetchUserData()
    } else {
      toast({
        title: 'No changes',
        description: 'No changes were made to your profile.',
      })
    }
  } catch (error) {
    toast({
      title: 'Error updating profile',
      description: 'Unable to update your profile. Please try again later.',
      variant: 'destructive',
    })
  }
})

fetchUserData()
</script>

<template>
  <div>
    <h3 class="text-lg font-medium">Profile</h3>
    <p class="text-sm text-muted-foreground">Manage your account settings and preferences.</p>
  </div>
  <Separator class="my-6" />

  <form @submit.prevent="onSubmit" class="space-y-8">
    <!-- Email Section -->
    <div class="space-y-4">
      <div class="flex justify-between items-center">
        <div>
          <h4 class="text-sm font-medium">Email</h4>
          <p class="text-sm text-muted-foreground">{{ userData.email }}</p>
        </div>
        <Button @click="isChangingEmail = !isChangingEmail" variant="outline" type="button">
          {{ isChangingEmail ? 'Cancel' : 'Change Email' }}
        </Button>
      </div>
      <div v-if="isChangingEmail" class="space-y-4">
        <FormField v-slot="{ field }" name="currentPassword">
          <FormItem>
            <FormLabel>Current Password</FormLabel>
            <FormControl>
              <Input type="password" placeholder="Enter your current password" v-bind="field" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ field }" name="email">
          <FormItem>
            <FormLabel>New Email</FormLabel>
            <FormControl>
              <Input type="email" placeholder="Enter your new email" v-bind="field" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
      </div>
    </div>

    <Separator />

    <!-- Password Section -->
    <div class="space-y-4">
      <div class="flex justify-between items-center">
        <div>
          <h4 class="text-sm font-medium">Password</h4>
          <p class="text-sm text-muted-foreground">Change your password</p>
        </div>
        <Button @click="isChangingPassword = !isChangingPassword" variant="outline" type="button">
          {{ isChangingPassword ? 'Cancel' : 'Change Password' }}
        </Button>
      </div>
      <div v-if="isChangingPassword" class="space-y-4">
        <FormField v-slot="{ field }" name="currentPassword">
          <FormItem>
            <FormLabel>Current Password</FormLabel>
            <FormControl>
              <Input type="password" placeholder="Enter your current password" v-bind="field" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ field }" name="newPassword">
          <FormItem>
            <FormLabel>New Password</FormLabel>
            <FormControl>
              <Input type="password" placeholder="Enter new password" v-bind="field" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ field }" name="confirmPassword">
          <FormItem>
            <FormLabel>Confirm New Password</FormLabel>
            <FormControl>
              <Input type="password" placeholder="Confirm new password" v-bind="field" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
      </div>
    </div>

    <Separator />

    <!-- Gender Section -->
    <FormField v-slot="{ field }" name="gender">
      <FormItem>
        <FormLabel>Gender</FormLabel>
        <Select v-bind="field">
          <FormControl>
            <SelectTrigger>
              <SelectValue :placeholder="userData.gender || 'Select your gender'" />
            </SelectTrigger>
          </FormControl>
          <SelectContent>
            <SelectGroup>
              <SelectLabel>Gender</SelectLabel>
              <SelectItem value="Male">Male</SelectItem>
              <SelectItem value="Female">Female</SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
        <FormDescription>Please select your gender.</FormDescription>
        <FormMessage />
      </FormItem>
    </FormField>

    <Button type="submit" class="w-1/6">Save</Button>
  </form>
</template>