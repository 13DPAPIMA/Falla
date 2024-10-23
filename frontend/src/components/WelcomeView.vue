<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router'; 

defineProps<{ msg: string }>();

const router = useRouter();
const count = ref(0);

// Importing images
import SunnyCloud1 from '../assets/Cloud_2.png';
import SunnyCloud2 from '../assets/Sun.png';
import SunnyCloud3 from '../assets/Cloud_3.png';
import RainyCloud1 from '../assets/Rainy_Cloud.png';
import RainyCloud2 from '../assets/Rainy_Cloud.png';
import RainyCloud3 from '../assets/Rainy_Cloud.png';
import CloudyCloud1 from '../assets/Cloud_1.png';
import CloudyCloud2 from '../assets/Cloud_2.png';
import CloudyCloud3 from '../assets/Cloud_3.png';
import DefaultCloud1 from '../assets/Smile.png';
import DefaultCloud2 from '../assets/Smile.png';
import DefaultCloud3 from '../assets/Smile.png';

const cloudImages = ref([DefaultCloud1, DefaultCloud2, DefaultCloud3]);

const fetchWeather = async () => {
  const apiKey = 'f75f002797deaf4537592f99f719afc5';
  const city = 'Rīga';

  try {
    const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`);
    const data = await response.json();

    if (data.weather && data.weather.length > 0) {
      const weatherCondition = data.weather[0].main.toLowerCase();

      switch (weatherCondition) {
        case 'clear':
          cloudImages.value = [SunnyCloud1, SunnyCloud2, SunnyCloud3];
          break;
        case 'rain':
          cloudImages.value = [RainyCloud1, RainyCloud2, RainyCloud3];
          break;
        case 'clouds':
          cloudImages.value = [CloudyCloud1, CloudyCloud2, CloudyCloud3];
          break;
        default:
          cloudImages.value = [DefaultCloud1, DefaultCloud2, DefaultCloud3];
      }
    }
  } catch (error) {
    console.error('Error fetching weather data:', error);
  }
};

onMounted(fetchWeather);

const navigateToOtherPage = () => {
  router.push('/home');
};
</script>

<template>
  <div class="welcome-page">
    <h1 class="title">Falla</h1>
    <h2 class="subtitle">Your weather guide</h2>
    <div class="floating-clouds">
      <img :src="cloudImages[0]" class="cloud cloud_1" alt="Cloud 1">
      <img :src="cloudImages[1]" class="cloud cloud_2" alt="Cloud 2">
      <img :src="cloudImages[2]" class="cloud cloud_3" alt="Cloud 3">

      <img src="../assets/Jeans_Object.png" class="cloud cloud_11" alt="Cloud 1">
      <img src="../assets/Boots_Object.png" class="cloud cloud_22" alt="Cloud 2">
      <img src="../assets/T-Shirt_Object.png" class="cloud cloud_33" alt="Cloud 3">
    </div>

    <button class="round-button" @click="navigateToOtherPage">Let's begin!</button>
  </div>
</template>

<style scoped>
.welcome-page {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  height: 100vh;
  position: relative;
  overflow: hidden;
  padding-top: 20px;
}


.round-button {
  position: absolute;
  top: 93%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 180px;
  height: 70px;
  border-radius: 100%;
  background-color: #E5C9AA;
  color: #73695C;
  border: 3px solid #000000;
  font-size: 1.5rem;
  font-weight: bold;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 5px;
  transition: background-color 0.3s;
  z-index: 2;
}

.round-button:hover {
  background-color: #498398;
}


.welcome-page::before,
.welcome-page::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: -1;
  background-size: cover;
  background-position: center;
}

.welcome-page::before {
  background-image: url('src/assets/Cloud_Background_1.jpg');
  opacity: 0.6;
}

.welcome-page::after {
  background-color: rgb(247, 246, 222);
  opacity: 0.5;
}

.title, .subtitle {
  color: #2b2a29;
  font-weight: bold;
}

.title {
  font-size: 7rem;
  margin: 0;
}

.subtitle {
  font-size: 4.5rem;
  margin: -80px 0;
}

.welcome-page h1,
.welcome-page h2 {
  text-align: center;
}

.floating-clouds {
  position: relative;
  width: 100%;
  height: 100%;
}


.cloud {
  position: absolute;
  width: 400px;
  z-index: 1;
}

.cloud_1 {
  top: 0px;
  left: 10%;
  animation: float 6s ease-in-out infinite;
}

.cloud_2 {
  top: 200px;
  left: 35%;
  transform: translateX(-50%);
  animation: float 5s ease-in-out infinite;
}

.cloud_3 {
  top: 70px;
  left: 65%;
  animation: float 4s ease-in-out infinite;
}

.cloud_11 {
  top: 100px;
  left: 25%;
  animation: float 6s ease-in-out infinite;
  width: 200px;
  z-index: 1;
}

.cloud_22 {
  top: 250px;
  left: 35%;
  transform: translateX(-50%);
  animation: float 5s ease-in-out infinite;
  width: 190px;
  z-index: 0;
}

.cloud_33 {
  top: 130px;
  left: 70%;
  animation: float 4s ease-in-out infinite;
  width: 160px;
  z-index: 0;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-20px);
  }
}
</style>
