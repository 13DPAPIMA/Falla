<template>
  <div>
    <div v-if="errorMessage" class="error-message">
      {{ errorMessage }}
    </div>
    
    <div class="weather-container" v-if="weather">
      <h2>{{ weather.name }} Weather Status</h2>
      <hr>
      <div class="time">
        <div>{{ formattedTime }}</div>
        <div>{{ formattedDate }}</div>
        <hr>
        <div>{{ weather.weather[0].description}}</div>
      </div>
      <div class="weather-forecast">
        <img
          :src="`https://openweathermap.org/img/w/${weather.weather[0].icon}.png`"
          class="weather-icon"
          alt="Weather Icon"
        />
        <hr>
        Maximum temperatura - {{ weather.main.temp_max }}°C
        <br>
        Minimum temperatura - {{ weather.main.temp_min }}°C
      </div>
      <div class="time">
        <div>Humidity: {{ weather.main.humidity }} %</div>
        <div>Wind: {{ weather.wind.speed }} km/h</div>
      </div>
    </div>
    
    <div v-else>
      Loading weather data...
    </div>
  </div>
</template>

<script>
import api from '@/api.js';

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
.weather-container {
  font-family: Arial, sans-serif;
}

.time {
  margin-top: 10px;
}
.min-temperature {
  color: #808080;
  margin-left: 10px;
}
.error-message {
  color: red;
  font-weight: bold;
}
</style>
