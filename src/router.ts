import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import HelloWorld from '@/components/ui/HelloWorld.vue';
import Weather from '@/components/Weather.vue';
import Register from '@/components/auth/Register.vue';
import Login from '@/components/auth/Login.vue';
import Profile from '@/components/Profile.vue';
import StylistPanel from './components/StylistPanel.vue';

const routes: Array<RouteRecordRaw> = [
    {
        path: '/',
        name: 'Home',
        component: HelloWorld,
    },
    {
        path: '/register',
        name: 'register',
        component: Register
    },
    {
        path: '/login',
        name: 'login',
        component: Login
    },
    {
        path: '/profile',
        name: 'profile',
        component: Profile
    },
    {
        path: '/weather',
        name: 'Weather',
        component: Weather,
    },
    {
        path: '/stylist-panel',
        name: 'Stylist-panel',
        component: StylistPanel,
    },
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});

export default router;
