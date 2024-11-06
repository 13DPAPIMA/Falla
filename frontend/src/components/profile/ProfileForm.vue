<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'

import { Input } from '@/components/ui/input'
import { FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
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
import { User, Mail, Lock } from 'lucide-vue-next'
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
    localStorage.removeItem('token')
    localStorage.removeItem('role')
    router.push('/')
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
      await api.put('api/user', updateData)  // TODO: add email verification

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
  <div class="max-w-3xl mx-auto px-4 py-4">
    <form @submit.prevent="onSubmit" class="space-y-12">
      <section>
        <h2 class="text-2xl font-semibold mb-2">General Information</h2>
        <p class="text-muted-foreground mb-6">Update your basic profile details</p>
        <div class="flex items-center space-x-4">
          <User class="text-muted-foreground" />
          <div class="flex-grow">
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
          </div>
        </div>
      </section>

      <section>
        <h2 class="text-2xl font-semibold mb-2">Security Settings</h2>
        <p class="text-muted-foreground mb-6">Manage your email and password</p>
        <div class="space-y-8">
          <div class="flex items-center space-x-4">
            <Mail class="text-muted-foreground" />
            <div class="flex-grow">
              <h3 class="text-lg font-medium">Email</h3>
              <p class="text-sm text-muted-foreground">{{ userData.email }}</p>
            </div>
            <Button @click="isChangingEmail = !isChangingEmail" variant="outline" type="button">
              {{ isChangingEmail ? 'Cancel' : 'Change' }}
            </Button>
          </div>
          <div v-if="isChangingEmail" class="ml-8 space-y-4">
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
          <div class="flex items-center space-x-4">
            <Lock class="text-muted-foreground" />
            <div class="flex-grow">
              <h3 class="text-lg font-medium">Password</h3>
              <p class="text-sm text-muted-foreground">Change your password</p>
            </div>
            <Button @click="isChangingPassword = !isChangingPassword" variant="outline" type="button">
              {{ isChangingPassword ? 'Cancel' : 'Change' }}
            </Button>
          </div>
          <div v-if="isChangingPassword" class="ml-8 space-y-4">
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
      </section>

      <div class="flex justify-end">
        <Button type="submit">Save Changes</Button>
      </div>
    </form>
  </div>
</template>