<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useToast } from '@/components/ui/toast/use-toast';
import api from '@/api.ts';
import router from '@/router.ts';
import { Button } from "@/components/ui/button";

const { toast } = useToast();

// Ref for storing wardrobe items
const wardrobeItems = ref<Array<any>>([]);
const searchQuery = ref<string>('');

// For modal control
const showAddModal = ref<boolean>(false);

// Fetch wardrobe items from API
const fetchWardrobeItems = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      router.push('/login');
      return; // Exit function early if token is not available
    }

    const response = await api.get('/api/wardrobe/items', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    wardrobeItems.value = response.data;

  } catch (error) {
    toast({
      title: 'Error fetching wardrobe items',
      description: 'Unable to load your wardrobe items. Please try again later.',
    });
  }
};

// Filter wardrobe items by search query
const filteredWardrobeItems = computed(() => {
  return wardrobeItems.value.filter(item =>
    item.clothing.type.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

// Delete a wardrobe item
const deleteItem = async (id: number) => {
  const confirmed = confirm('Are you sure you want to delete this item?');
  if (!confirmed) return;

  try {
    const token = localStorage.getItem('token');
    if (!token) {
      router.push('/login');
      return;
    }

    await api.delete(`/api/wardrobe/items/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    toast({
      title: 'Item Deleted',
      description: 'The wardrobe item has been deleted successfully.',
    });

    // Refresh wardrobe items after deletion
    fetchWardrobeItems();

  } catch (error) {
    toast({
      title: 'Error deleting item',
      description: 'Unable to delete the wardrobe item. Please try again.',
    });
  }
};

// Open modal to add a new item
const openAddModal = () => {
  showAddModal.value = true;
};

// Fetch wardrobe items on component mount
onMounted(async () => {
  await fetchWardrobeItems();
});
</script>

<template>
  <div class="wardrobe">
    <div class="header">
      <h1>Your Wardrobe</h1>
      <input v-model="searchQuery" placeholder="Search..." />
    </div>

    <!-- Display wardrobe items in a grid -->
    <div class="grid">
      <div v-for="item in filteredWardrobeItems" :key="item.id" class="wardrobe-item">
        <img :src="item.clothing.photo.photo_url" alt="Clothing Photo" />
        <p>{{ item.clothing.type }} - {{ item.clothing.color }}</p>

        <div class="item-actions">
          <Button @click="() => editItem(item)">Edit</Button>
          <Button @click="() => deleteItem(item.id)">Delete</Button>
        </div>
      </div>
    </div>

    <!-- Add New Item Button -->
    <Button class="add-item-btn" @click="openAddModal">Add New Item</Button>

    <!-- Add Item Modal Component -->
    <AddItemModal v-if="showAddModal" @close="showAddModal = false" @itemAdded="fetchWardrobeItems" />
  </div>
</template>

<style scoped>
.wardrobe {
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.wardrobe-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.wardrobe-item img {
  width: 150px;
  height: 150px;
  object-fit: cover;
  border-radius: 10px;
}

.item-actions {
  margin-top: 10px;
  display: flex;
  justify-content: space-between;
}

.add-item-btn {
  margin-top: 20px;
  background-color: #6200ea;
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
}
</style>
