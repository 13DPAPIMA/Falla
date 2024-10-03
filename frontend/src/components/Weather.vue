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

      <form @submit.prevent="uploadImage" enctype="multipart/form-data">
        <input type="file" @change="handleFileUpload" />
        <button type="submit">Upload Image</button>
      </form>
      
      <div v-if="uploadedImageUrl">
        <h3>Uploaded Image:</h3>
        <img :src="uploadedImageUrl" alt="Uploaded Image" />
      </div>

    </div>

    <div v-else>
      <div class="loading-message">No weather data for today.</div>
    </div>
  </div>


  
</template>


<script>
import axios from 'axios';

export default {
  data() {
    return {
      weather: null,
      city: '',
      errorMessage: null,
      todayForecast: [], // массив для хранения прогноза на сегодня
    };
  },
  methods: {
    async fetchWeather() {
      const cityToFetch = this.city || 'Riga';

      try {
        const response = await axios.get(`/weather?city=${encodeURIComponent(cityToFetch)}`);
        this.weather = response.data;
        this.errorMessage = null;

        // После получения данных отфильтруем прогноз на сегодня
        this.filterTodayForecast();
      } catch (error) {
        console.error('Error fetching weather data:', error);

        if (error.response) {
          this.errorMessage = `Error ${error.response.status}: ${error.response.statusText}`;
        } else if (error.request) {
          this.errorMessage = 'No response received from server.';
        } else {
          this.errorMessage = `Error: ${error.message}`;
        }

        this.weather = null;
      }
    },
    filterTodayForecast() {
      if (!this.weather || !this.weather.list) return;

      const today = new Date().toISOString().split('T')[0]; // Получаем текущую дату в формате YYYY-MM-DD

      // Фильтруем только прогнозы на сегодняшний день
      this.todayForecast = this.weather.list.filter(forecast => {
        const forecastDate = forecast.dt_txt.split(' ')[0]; // Дата прогноза
        return forecastDate === today;
      });
    },
    formatTime(dateTime) {
      const options = { hour: '2-digit', minute: '2-digit' };
      return new Date(dateTime).toLocaleTimeString(undefined, options);
    },
  },
  mounted() {
    this.city = 'Riga';
    this.fetchWeather();
  },
};
</script>



<style scoped>
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