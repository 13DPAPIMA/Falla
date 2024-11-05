import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import Welcome from '@/components/WelcomeView.vue';
import Home from '@/components/HomeView.vue';
import Register from '@/components/auth/Register.vue';
import Login from '@/components/auth/Login.vue';
import VerifyEmail from '@/components/auth/VerifyEmail.vue';
import EmailVerificationHandler from '@/components/auth/EmailVerificationHandler.vue';
import Profile from '@/components/profile/Profile.vue';
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
        path: '/verify-email',
        name: 'verifyEmail',
        component: VerifyEmail,
        meta: { requiresAuth: true }
    },
    {
        path: '/verify-email-handler',
        name: 'verifyEmailHandler',
        component: EmailVerificationHandler
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
    {
        path: '/stylist-panel',
        name: 'stylistPanel',
        component: StylistPanel,
        meta: { requiresAuth: true, role: 'stylist' }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'notFound',
        component: NotFound
    }
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = await authStore.checkAuth();

    if (to.meta.requiresAuth && !isAuthenticated) {
        return next({ name: 'login' });
    }

    if (to.meta.requiresGuest && isAuthenticated) {
        return next({ name: 'home' });
    }

    if (to.meta.role && to.meta.role !== authStore.user?.role) {
        return next({ name: 'notFound' });
    }

    next();
});

export default router;