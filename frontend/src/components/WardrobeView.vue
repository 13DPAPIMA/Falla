<template>
    <div class="wardrobe">
      <div class="header">
        <h1>Your Wardrobe</h1>
        <input v-model="searchQuery" placeholder="Search..." />
      </div>
  
      <div class="grid">
        <!-- Loop through wardrobe items and display clothing with their photos -->
        <div v-for="item in filteredWardrobeItems" :key="item.id" class="wardrobe-item">
          <img :src="item.clothing.photo.photo_url" alt="Clothing Photo" />
          <p>{{ item.clothing.type }} - {{ item.clothing.color }}</p>
  
          <div class="item-actions">
            <button @click="editItem(item)">Edit</button>
            <button @click="deleteItem(item.id)">Delete</button>
          </div>
        </div>
      </div>
  
      <button @click="openAddModal" class="add-item-btn">Add New Item</button>
  
      <!-- Add new item modal -->
      <AddItemModal v-if="showAddModal" @close="showAddModal = false" @itemAdded="fetchWardrobeItems" />
    </div>
  </template>
  
  <script>
  import wardrobeService from '../services/wardrobeService';
  import AddItemModal from './AddItemModal.vue';
  
  export default {
    data() {
      return {
        wardrobeItems: [],
        searchQuery: '',
        showAddModal: false
      };
    },
    computed: {
      filteredWardrobeItems() {
        return this.wardrobeItems.filter(item =>
          item.clothing.type.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      }
    },
    methods: {
      fetchWardrobeItems() {
        wardrobeService.getWardrobeItems()
          .then(response => {
            this.wardrobeItems = response.data;
          })
          .catch(error => {
            console.error("Error fetching wardrobe items:", error);
          });
      },
      deleteItem(id) {
        if (confirm("Are you sure you want to delete this item?")) {
          wardrobeService.deleteWardrobeItem(id)
            .then(() => {
              this.fetchWardrobeItems();
            })
            .catch(error => {
              console.error("Error deleting wardrobe item:", error);
            });
        }
      },
      editItem(item) {
        alert(`Editing item: ${item.clothing.type}`);
        // Open edit modal or redirect to editing page based on your app logic.
      },
      openAddModal() {
        this.showAddModal = true;
      }
    },
    mounted() {
      this.fetchWardrobeItems();
    },
    components: {
      AddItemModal
    }
  };
  </script>
  
  <style scoped>
  /* Styling for wardrobe view */
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
  