import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import Welcome from '@/components/WelcomeView.vue';
import Home from '@/components/HomeView.vue';
import Register from '@/components/auth/Register.vue';
import Login from '@/components/auth/Login.vue';
import Profile from '@/components/profile/Profile.vue'
import Wardrobe from '@/components/WardrobeView.vue';
import NotFound from '@/components/NotFound.vue';
import StylistPanel from '@/components/StylistPanel.vue';

const routes: Array<RouteRecordRaw> = [
    {
        path: '/',
        name: 'welcome',
        component: Welcome,
        meta: { requiresGuest: true }
    },
    {
        path: '/home',
        name: 'home',
        component: Home
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { requiresGuest: true }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { requiresGuest: true }
    },
    {
        path: '/profile',
        name: 'profile',
        component: Profile,
        meta: { requiresAuth: true }
    },
    {
        path: '/wardrobe',
        name: 'wardrobe',
        component: Wardrobe,
        meta: { requiresAuth: true }
    },
    ,
    {
        path: '/stylist-panel',
        name: 'Stylist Panel',
        component: StylistPanel,
        meta: { requiresAuth: true, role: 'stylist' }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: NotFound,
    },
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const role = localStorage.getItem('role');

    // If route requires authentication and user is not logged in
    if (to.meta.requiresAuth && !token) {
        return next({ name: 'login' }); // Redirect to login if not authenticated
    }

    // If route requires guest access and user is logged in
    if (to.meta.requiresGuest && token) {
        return next({ name: 'home' }); // Redirect logged-in users to the home page
    }

    // If route requires a specific role and user doesn't have it
    if (to.meta.role && to.meta.role !== role) {
        return next({ name: 'NotFound' });
    }

    next(); // Proceed to the route
});

export default router;
