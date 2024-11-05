import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api'

interface User {
    id: number
    email: string
    email_verified_at: string | null
    role: string
    gender: string
    created_at: string
    updated_at: string
}

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(null)
    const token = ref<string | null>(null)

    function setUser(userData: User) {
        user.value = userData
    }

    function setToken(tokenValue: string) {
        token.value = tokenValue
        localStorage.setItem('token', tokenValue)
    }

    function clearAuth() {
        user.value = null
        token.value = null
        localStorage.removeItem('token')
    }

    async function logout() {
        try {
            await api.post('/api/logout')
        } catch (error) {
            console.error('Logout failed:', error)
        } finally {
            clearAuth()
        }
    }

    async function checkAuth() {
        const storedToken = localStorage.getItem('token')
        if (storedToken) {
            token.value = storedToken
            try {
                const response = await api.get('/api/user')
                setUser(response.data)
                return true
            } catch (error) {
                console.error('Auth check failed:', error)
                clearAuth()
            }
        }
        return false
    }

    return { user, token, setUser, setToken, clearAuth, logout, checkAuth }
})