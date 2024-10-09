<template>
  <div class="weather-wrapper">
    <div class="city-input">
      <input v-model="city" type="text" placeholder="Enter city" />
      <button @click="fetchWeather">Get Weather</button>
    </div>

    <div v-if="errorMessage" class="error-message">
      {{ errorMessage }}
    </div>

    <div v-if="todayForecast && todayForecast.length" class="weather-container">
      <h2 class="city-name">{{ weather.city.name }} Today's Weather</h2>
      <hr class="divider">

      <div v-for="(forecast, index) in todayForecast" :key="index" class="forecast-item">
        <!-- Информация о погоде -->
        <div class="time-info">
          <div class="formatted-time">{{ formatTime(forecast.dt_txt) }}</div>
          <div class="weather-description">{{ forecast.weather[0].description }}</div>
        </div>
        <div class="temperature-info">
          <div>Temp: {{ forecast.main.temp }}°C (Feels like {{ forecast.main.feels_like }}°C)</div>
          <div>Max: {{ forecast.main.temp_max }}°C | Min: {{ forecast.main.temp_min }}°C</div>
        </div>
        <div class="additional-info">
          <div>Humidity: {{ forecast.main.humidity }}%</div>
          <div>Wind: {{ forecast.wind.speed }} km/h, gusts up to {{ forecast.wind.gust }} km/h</div>
          <div>Cloudiness: {{ forecast.clouds.all }}%</div>
          <div>Rain: {{ forecast.rain?.['3h'] || 0 }} mm</div>
        </div>
        <img
            v-if="forecast.weather[0]?.icon"
            :src="`https://openweathermap.org/img/w/${forecast.weather[0].icon}.png`"
            class="weather-icon"
            alt="Weather Icon"
        />
        <hr class="divider">
      </div>
    </div>

    <div v-else>
      <div class="loading-message">No weather data for today.</div>
    </div>
  </div>


  <ClothingSuggestions v-if="weather" :weather="weather" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ClothingSuggestions from './ClothingSuggestions.vue';

const weather = ref(null);
const city = ref('Riga');
const errorMessage = ref<string | null>(null);
const todayForecast = ref<any[]>([]);

const fetchWeather = async () => {
  const cityToFetch = city.value || 'Riga';

  try {
    const response = await axios.get(`/api/weather?city=${encodeURIComponent(cityToFetch)}`);
    weather.value = response.data;
    errorMessage.value = null;

    filterTodayForecast();
  } catch (error: any) {
    console.error('Error fetching weather data:', error);

    if (error.response) {
      errorMessage.value = `Error ${error.response.status}: ${error.response.statusText}`;
    } else if (error.request) {
      errorMessage.value = 'No response received from server.';
    } else {
      errorMessage.value = `Error: ${error.message}`;
    }

    weather.value = null;
  }
};

const filterTodayForecast = () => {
  if (!weather.value || !weather.value.list) return;

  const today = new Date().toISOString().split('T')[0];
  todayForecast.value = weather.value.list.filter((forecast: any) => {
    const forecastDate = forecast.dt_txt.split(' ')[0];
    return forecastDate === today;
  });
};

const formatTime = (dateTime: string) => {
  const options: Intl.DateTimeFormatOptions = { hour: '2-digit', minute: '2-digit' };
  return new Date(dateTime).toLocaleTimeString(undefined, options);
};

onMounted(() => {
  fetchWeather();
});
</script>

<style scoped>

input[type="file"] {
  margin-bottom: 10px;
}

button {
  background-color: #007bff;
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
}

button:hover {
  background-color: #0056b3;
}

img {
  margin-top: 20px;
  max-width: 300px;
  border: 1px solid #ccc;
}

.clothing-item {
  border: 1px solid #ccc;
  padding: 20px;
  margin-bottom: 20px;
}

.clothing-item img {
  width: 150px;
  height: auto;
}

.weather-container {
  font-family: Arial, sans-serif;
  border: 1px solid #ccc;
  padding: 20px;
  margin-top: 20px;
  border-radius: 8px;
  background-color: #f9f9f9;
  text-align: center;
}

.city-input {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.city-input input {
  padding: 8px;
  font-size: 16px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

.city-input button {
  padding: 8px 12px;
  margin-left: 10px;
  font-size: 16px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.city-input button:hover {
  background-color: #0056b3;
}

.error-message {
  color: red;
  font-weight: bold;
}

.loading {
  font-size: 18px;
  color: #007bff;
}

.weather-wrapper {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.error-message {
  color: #d9534f;
  font-weight: bold;
  margin-bottom: 20px;
}

.weather-container {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 20px;
  background-color: #f9f9f9;
}

.city-name {
  font-size: 24px;
  color: #333;
  margin-bottom: 10px;
}

.weather-details {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.time-info {
  text-align: center;
}

.formatted-time, .formatted-date {
  font-size: 18px;
  color: #666;
}

.weather-description {
  font-size: 20px;
  color: #333;
  margin: 10px 0;
}

.weather-forecast {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.weather-icon {
  width: 100px;
  height: 100px;
  margin: 10px 0;
}

.temperature-info {
  font-size: 18px;
  color: #333;
}

.additional-info {
  font-size: 16px;
  color: #666;
  margin-top: 20px;
}

.loading-message {
  text-align: center;
  font-size: 18px;
  color: #666;
}

.divider {
  margin: 20px 0;
}
</style>
