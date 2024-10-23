<template>
  <div class="clothing-suggestions">
    <h1>Clothing Suggestions</h1>
    <div v-for="item in clothing" :key="item.id" class="clothing-item">
      <h3>{{ item.type }}</h3>
      <img :src="item.photo.photo_url" alt="Clothing Photo" />
      <p>Style: {{ item.style }}</p>
      <p>Color: {{ item.color }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  weather: Object
});

const clothing = ref<any[]>([]);

watch(() => props.weather, async (newWeather) => {
  if (newWeather) {
    await fetchClothingSuggestions(newWeather);
  }
});

const fetchClothingSuggestions = async (weatherData: any) => {
  try {
    const response = await axios.post('/api/clothing-suggestions', { weather: weatherData });
    clothing.value = response.data;
  } catch (error) {
    console.error('Error fetching clothing suggestions:', error);
  }
};
</script>

<style scoped>
.clothing-item {
  border: 1px solid #ccc;
  padding: 20px;
  margin-bottom: 20px;
}

.clothing-item img {
  width: 150px;
  height: auto;
}
</style>
