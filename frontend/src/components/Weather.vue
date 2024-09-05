<template>
  <div class="weather-wrapper">
    <div v-if="errorMessage" class="error-message">
      {{ errorMessage }}
    </div>
    
    <div class="weather-container" v-if="weather">
      <h2 class="city-name">{{ weather.name }} Weather Status</h2>
      <hr class="divider">
      <div class="weather-details">
        <div class="time-info">
          <div class="formatted-time">{{ formattedTime }}</div>
          <div class="formatted-date">{{ formattedDate }}</div>
          <hr class="divider">
          <div class="weather-description">{{ weather.weather[0].description }}</div>
        </div>
        <div class="weather-forecast">
          <img
            :src="`https://openweathermap.org/img/w/${weather.weather[0].icon}.png`"
            class="weather-icon"
            alt="Weather Icon"
          />
          <div class="temperature-info">
            <div>Max Temp: {{ weather.main.temp_max }}°C</div>
            <div>Min Temp: {{ weather.main.temp_min }}°C</div>
          </div>
        </div>
        <div class="additional-info">
          <div>Humidity: {{ weather.main.humidity }} %</div>
          <div>Wind: {{ weather.wind.speed }} km/h</div>
        </div>
      </div>
    
    </div>
    
    <div v-else>
      <div class="loading-message">Loading weather data...</div>
    </div>
  </div>
</template>

<script>
import api from '@/api.ts';

export default {
  data() {
    return {
      weather: null,
      currentTime: new Date(),
      errorMessage: null,
    };
  },
  computed: {
    formattedDate() {
      return this.currentTime.toLocaleDateString();
    },
    formattedTime() {
      return this.currentTime.toLocaleTimeString();
    },
  },
  mounted() {
    this.fetchWeather();
  },
  methods: {
    async fetchWeather() {
      try {
        const response = await api.get('/weather');
        
        if (!response.data || !response.data.weather || !Array.isArray(response.data.weather) || response.data.weather.length === 0) {
          throw new Error('Invalid weather data structure');
        }
        
        this.weather = response.data;
        this.currentTime = new Date();
      } catch (error) {
        console.error('Error fetching weather data:', error);
        
        if (error.response) {
          this.errorMessage = `Error ${error.response.status}: ${error.response.statusText}`;
        } else if (error.request) {
          this.errorMessage = 'No response received from server. Please check your connection.';
        } else {
          this.errorMessage = `Error: ${error.message}`;
        }
        
        this.weather = null;
      }
    },
  },
};
</script>

<style scoped>
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
