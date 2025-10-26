import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'
import App from './App.vue'
import VendingMachine from './components/VendingMachine.vue'
import AdminPanel from './components/AdminPanel.vue'
import './bootstrap'

// Configure axios
axios.defaults.baseURL = '/api'
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Router configuration
const routes = [
    { path: '/', component: VendingMachine },
    { path: '/admin', component: AdminPanel }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Create Vue app
const app = createApp(App)
app.use(router)
app.mount('#app')
