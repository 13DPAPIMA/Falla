<template>
  <div class="main-container">
    <!-- Приветственное сообщение и изображение -->
    <div class="welcome-section">
      <h1 class="welcome-title">WELCOME TO FALLA</h1>
      <p class="welcome-description">
        Application where you can get clothing recommendations based on weather
      </p>
      <img class="welcome-image" src="C:\Users\Артём\Desktop\Falla\Falla\frontend\public\clipart1798010.png" alt="Sunny Cloud" />
      <button @click="scrollToWeather" class="scroll-button">See the Weather</button>
    </div>

 
    <!-- Секция с погодой -->
    <div class="weather-section" ref="weatherSection">
      <div class="city-input">
        <input v-model="city" type="text" placeholder="Enter city" />
        <button @click="fetchWeather">Update Weather</button> <!-- Оставляем для возможного ручного поиска -->
      </div>
      
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
            <div class="weather-description">{{ weather.weather[0]?.description || 'No description available' }}</div>
          </div>
          <div class="temperature-info">Current temperature: {{ weather.main?.temp || 'N/A' }}°C</div>
          <div class="weather-forecast">
            <img
              v-if="weather.weather[0]?.icon"
              :src="`https://openweathermap.org/img/w/${weather.weather[0].icon}.png`"
              class="weather-icon"
              alt="Weather Icon"
            />
            <div class="temperature-info">
              <div>Max Temp: {{ weather.main?.temp_max || 'N/A' }}°C</div>
              <div>Min Temp: {{ weather.main?.temp_min || 'N/A' }}°C</div>
            </div>
          </div>
          <div class="additional-info">
            <div>Humidity: {{ weather.main?.humidity || 'N/A' }} %</div>
            <div>Wind: {{ weather.wind?.speed || 'N/A' }} km/h</div>
          </div>
        </div>
      </div>

      <div v-else>
        <div class="loading-message">Loading weather data...</div>
      </div>
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
    this.getLocationAndFetchWeather(); // Автоматически загружаем погоду при монтировании компонента
  },
  methods: {
    async fetchWeather(city = null, coords = null) {
      let url = '';
      if (coords) {
        url = `/weather?lat=${coords.latitude}&lon=${coords.longitude}`;
      } else {
        const cityToFetch = city || this.city || 'Riga';
        url = `/weather?city=${encodeURIComponent(cityToFetch)}`;
      }

      try {
        const response = await axios.get(url);
        this.weather = response.data;
        this.errorMessage = null;
      } catch (error) {
        this.weather = null;
        this.errorMessage = 'Error fetching weather data';
      }
    },
    getLocationAndFetchWeather() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          (position) => {
            const coords = {
              latitude: position.coords.latitude,
              longitude: position.coords.longitude,
            };
            this.fetchWeather(null, coords); // Получаем погоду по геолокации
          },
          (error) => {
            console.error('Geolocation error:', error);
            // Если геолокация недоступна, используем город по умолчанию
            this.fetchWeather('Riga');
          }
        );
      } else {
        // Если геолокация не поддерживается, используем город по умолчанию
        this.fetchWeather('Riga');
      }
    },
    scrollToWeather() {
      // Плавная прокрутка к блоку погоды
      this.$refs.weatherSection.scrollIntoView({ behavior: 'smooth' });
    }
  }
};
</script>

<style scoped>
/* Стартовая секция с приветствием */
.welcome-section {
  text-align: center;
  padding: 100px 20px;
  background-color: #f0f8ff;
  height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.welcome-title {
  font-size: 48px;
  color: #333;
  font-weight: bold;
  margin-bottom: 20px;
}

.welcome-description {
  font-size: 18px;
  color: #555;
  margin-bottom: 40px;
}

.welcome-image {
  width: 200px;
  height: auto;
  margin-bottom: 30px;
}

.scroll-button {
  padding: 15px 30px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 18px;
  transition: background-color 0.3s;
}

.scroll-button:hover {
  background-color: #0056b3;
}

/* Секция погоды */
.weather-section {
  padding: 40px;
  background-color: #fff;
  text-align: center;
  align-items: center;
  justify-content: center;
}

.weather-container {
  font-family: Arial, sans-serif;
  border: 1px solid #ccc;
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-color: #f9f9f9;
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

.loading-message {
  font-size: 18px;
  color: #007bff;
}

.divider {
  margin: 20px 0;
}

.weather-icon {
  width: 100px;
  height: 100px;
  margin: 10px 0;
}
</style>
