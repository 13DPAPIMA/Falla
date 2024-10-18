<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white p-6 rounded shadow-md w-80">
      <h2 class="text-xl font-bold mb-4">{{ itemToEdit ? 'Edit Item' : 'Add Item' }}</h2>
      
      <form @submit.prevent="submitForm">
        <div>
          <label for="clothing_id" class="block mb-1">Clothing Item</label>
          <select v-model="selectedClothingId" id="clothing_id" required class="border rounded w-full">
            <option value="">Select a clothing item</option>
            <option v-for="clothing in clothingItems" :key="clothing.id" :value="clothing.id">
              {{ clothing.name }}
            </option>
          </select>
        </div>
        <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded mt-4">Save</button>
      </form>

      <button @click="$emit('close')" class="text-gray-500 mt-4">Cancel</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, defineProps, onMounted } from 'vue';
import api from '@/api'; // Replace with your API utility
import { useToast } from '@/components/ui/toast/use-toast'; // Toast notification utility

const props = defineProps<{
  itemToEdit: any | null;
  clothingItems: Array<{ id: number; name: string; }>;
}>();

const selectedClothingId = ref<string>('');
const { toast } = useToast();

// Watch for item to edit
watch(() => props.itemToEdit, (newVal) => {
  if (newVal) {
    selectedClothingId.value = newVal.clothing_id;
  } else {
    selectedClothingId.value = '';
  }
});

// Fetch clothing items to display in dropdown
const fetchClothingItems = async () => {
  try {
    const response = await api.get('/api/clothing-items');
    return response.data;
  } catch (error) {
    toast({
      title: 'Error',
      description: 'Unable to load clothing items. Please try again later.',
    });
  }
};

const submitForm = async () => {
  try {
    if (props.itemToEdit) {
      await api.put(`/api/wardrobe-items/${props.itemToEdit.id}`, {
        clothing_id: selectedClothingId.value,
      });
      toast({
        title: 'Item Updated',
        description: 'The item was successfully updated in your wardrobe.',
      });
    } else {
      await api.post('/api/wardrobe-items', {
        clothing_id: selectedClothingId.value,
      });
      toast({
        title: 'Item Added',
        description: 'The item was successfully added to your wardrobe.',
      });
    }
    selectedClothingId.value = ''; // Reset the form
    $emit('itemAdded'); // Notify parent to refresh the list
  } catch (error) {
    toast({
      title: 'Error',
      description: 'Unable to add/update the item. Please try again later.',
    });
  }
};

onMounted(fetchClothingItems);
</script>

<style scoped>
/* Add your styles here */
</style>
