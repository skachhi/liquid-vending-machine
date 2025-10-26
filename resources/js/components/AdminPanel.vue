<template>
    <div class="container">
        <div class="card">
            <div class="admin-header">
                <h1><i class="fas fa-cog"></i> Admin Panel</h1>
                <router-link to="/" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Machine
                </router-link>
            </div>

            <!-- Login Form -->
            <div v-if="!isAuthenticated" class="login-section">
                <h2>Admin Login</h2>
                <form @submit.prevent="login" class="login-form">
                    <div class="form-group">
                        <label>Email:</label>
                        <input v-model="loginForm.email" type="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password:</label>
                        <input v-model="loginForm.password" type="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        {{ loading ? 'Logging in...' : 'Login' }}
                    </button>
                </form>
            </div>

            <!-- Admin Dashboard -->
            <div v-else class="admin-dashboard">
                <!-- Navigation Tabs -->
                <div class="admin-tabs">
                    <button 
                        v-for="tab in tabs" 
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="['tab-btn', { active: activeTab === tab.id }]"
                    >
                        <i :class="tab.icon"></i> {{ tab.name }}
                    </button>
                </div>

                <!-- Products Management -->
                <div v-if="activeTab === 'products'" class="tab-content">
                    <div class="section-header">
                        <h2>Products Management</h2>
                        <button @click="showAddProductModal = true" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Product
                        </button>
                    </div>

                    <div class="products-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in products" :key="product.id">
                                    <td>{{ product.id }}</td>
                                    <td>{{ product.name }}</td>
                                    <td>{{ product.category }}</td>
                                    <td>₹{{ product.price }}</td>
                                    <td>{{ product.stock }}</td>
                                    <td>
                                        <button @click="editProduct(product)" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="deleteProduct(product.id)" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Orders Management -->
                <div v-if="activeTab === 'orders'" class="tab-content">
                    <div class="section-header">
                        <h2>Orders Management</h2>
                        <button @click="fetchOrders" class="btn btn-primary">
                            <i class="fas fa-refresh"></i> Refresh
                        </button>
                    </div>

                    <div class="orders-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Payment Method</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in orders" :key="order.id">
                                    <td>{{ order.id }}</td>
                                    <td>{{ order.product_name }}</td>
                                    <td>{{ order.quantity }}</td>
                                    <td>₹{{ order.total_price }}</td>
                                    <td>{{ order.payment_method }}</td>
                                    <td>{{ formatDate(order.created_at) }}</td>
                                    <td>
                                        <span :class="['status-badge', order.status]">{{ order.status }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Analytics -->
                <div v-if="activeTab === 'analytics'" class="tab-content">
                    <div class="section-header">
                        <h2>Sales Analytics</h2>
                        <div class="date-range">
                            <input v-model="analyticsDateRange.start_date" type="date">
                            <span>to</span>
                            <input v-model="analyticsDateRange.end_date" type="date">
                            <button @click="fetchAnalytics" class="btn btn-primary">Update</button>
                        </div>
                    </div>

                    <div class="analytics-grid">
                        <div class="analytics-card">
                            <h3>Total Sales</h3>
                            <div class="analytics-value">₹{{ analytics.total_sales }}</div>
                        </div>
                        <div class="analytics-card">
                            <h3>Total Orders</h3>
                            <div class="analytics-value">{{ analytics.total_orders }}</div>
                        </div>
                        <div class="analytics-card">
                            <h3>Most Popular</h3>
                            <div class="analytics-value">{{ analytics.most_popular_product }}</div>
                        </div>
                        <div class="analytics-card">
                            <h3>Average Order</h3>
                            <div class="analytics-value">₹{{ analytics.average_order_value }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add/Edit Product Modal -->
            <div v-if="showAddProductModal || showEditProductModal" class="modal-overlay" @click="closeProductModal">
                <div class="modal-content" @click.stop>
                    <h3>{{ showAddProductModal ? 'Add Product' : 'Edit Product' }}</h3>
                    <form @submit.prevent="saveProduct" class="product-form">
                        <div class="form-group">
                            <label>Product Name:</label>
                            <input v-model="productForm.name" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Category:</label>
                            <select v-model="productForm.category" required>
                                <option value="Coffee">Coffee</option>
                                <option value="Tea">Tea</option>
                                <option value="Juice">Juice</option>
                                <option value="Water">Water</option>
                                <option value="Soda">Soda</option>
                                <option value="Energy Drink">Energy Drink</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Price (₹):</label>
                            <input v-model="productForm.price" type="number" min="1" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Stock:</label>
                            <input v-model="productForm.stock" type="number" min="0" required>
                        </div>
                        <div class="form-actions">
                            <button type="button" @click="closeProductModal" class="btn btn-danger">Cancel</button>
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, reactive } from 'vue'
import axios from 'axios'

export default {
    name: 'AdminPanel',
    setup() {
        const isAuthenticated = ref(false)
        const loading = ref(false)
        const activeTab = ref('products')
        const products = ref([])
        const orders = ref([])
        const showAddProductModal = ref(false)
        const showEditProductModal = ref(false)
        const editingProduct = ref(null)

        const loginForm = reactive({
            email: '',
            password: ''
        })

        const productForm = reactive({
            name: '',
            category: 'Coffee',
            price: 0,
            stock: 0
        })

        const analytics = reactive({
            total_sales: 0,
            total_orders: 0,
            most_popular_product: 'N/A',
            average_order_value: 0
        })

        const analyticsDateRange = reactive({
            start_date: new Date().toISOString().split('T')[0],
            end_date: new Date().toISOString().split('T')[0]
        })

        const tabs = [
            { id: 'products', name: 'Products', icon: 'fas fa-boxes' },
            { id: 'orders', name: 'Orders', icon: 'fas fa-shopping-cart' },
            { id: 'analytics', name: 'Analytics', icon: 'fas fa-chart-bar' }
        ]

        const login = async () => {
            loading.value = true
            try {
                const response = await axios.post('/admin/login', loginForm)
                if (response.data.success) {
                    isAuthenticated.value = true
                    localStorage.setItem('adminToken', response.data.token)
                    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
                    await fetchProducts()
                    await fetchOrders()
                    await fetchAnalytics()
                }
            } catch (error) {
                console.error('Login error:', error)
                alert('Invalid credentials')
            }
            loading.value = false
        }

        const fetchProducts = async () => {
            try {
                const response = await axios.get('/admin/products')
                products.value = response.data
            } catch (error) {
                console.error('Error fetching products:', error)
            }
        }

        const fetchOrders = async () => {
            try {
                const response = await axios.get('/admin/orders')
                orders.value = response.data
            } catch (error) {
                console.error('Error fetching orders:', error)
            }
        }

        const fetchAnalytics = async () => {
            try {
                const response = await axios.get('/admin/analytics', {
                    params: analyticsDateRange
                })
                Object.assign(analytics, response.data)
            } catch (error) {
                console.error('Error fetching analytics:', error)
            }
        }

        const editProduct = (product) => {
            editingProduct.value = product
            Object.assign(productForm, product)
            showEditProductModal.value = true
        }

        const saveProduct = async () => {
            try {
                if (showAddProductModal.value) {
                    await axios.post('/admin/products', productForm)
                } else {
                    await axios.put(`/admin/products/${editingProduct.value.id}`, productForm)
                }
                await fetchProducts()
                closeProductModal()
            } catch (error) {
                console.error('Error saving product:', error)
            }
        }

        const deleteProduct = async (productId) => {
            if (confirm('Are you sure you want to delete this product?')) {
                try {
                    await axios.delete(`/admin/products/${productId}`)
                    await fetchProducts()
                } catch (error) {
                    console.error('Error deleting product:', error)
                }
            }
        }

        const closeProductModal = () => {
            showAddProductModal.value = false
            showEditProductModal.value = false
            editingProduct.value = null
            Object.assign(productForm, {
                name: '',
                category: 'Coffee',
                price: 0,
                stock: 0
            })
        }

        const formatDate = (dateString) => {
            return new Date(dateString).toLocaleString()
        }

        onMounted(() => {
            const token = localStorage.getItem('adminToken')
            if (token) {
                isAuthenticated.value = true
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
                fetchProducts()
                fetchOrders()
                fetchAnalytics()
            }
        })

        return {
            isAuthenticated,
            loading,
            activeTab,
            products,
            orders,
            showAddProductModal,
            showEditProductModal,
            editingProduct,
            loginForm,
            productForm,
            analytics,
            analyticsDateRange,
            tabs,
            login,
            editProduct,
            saveProduct,
            deleteProduct,
            closeProductModal,
            formatDate,
            fetchOrders,
            fetchAnalytics
        }
    }
}
</script>

<style scoped>
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.login-section {
    max-width: 400px;
    margin: 0 auto;
}

.login-form {
    margin-top: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 12px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 16px;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #667eea;
}

.admin-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid #e9ecef;
}

.tab-btn {
    padding: 15px 25px;
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    color: #6c757d;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.tab-btn.active {
    color: #667eea;
    border-bottom-color: #667eea;
}

.tab-btn:hover {
    color: #667eea;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.products-table,
.orders-table {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

th,
td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.btn-sm {
    padding: 8px 12px;
    font-size: 14px;
    margin: 0 5px;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.completed {
    background: #d4edda;
    color: #155724;
}

.status-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.analytics-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
}

.analytics-card h3 {
    margin-bottom: 15px;
    font-size: 18px;
}

.analytics-value {
    font-size: 32px;
    font-weight: bold;
}

.date-range {
    display: flex;
    align-items: center;
    gap: 10px;
}

.product-form {
    margin-top: 20px;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 40px;
    border-radius: 15px;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

@media (max-width: 768px) {
    .admin-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .admin-tabs {
        flex-wrap: wrap;
    }
    
    .section-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .date-range {
        flex-direction: column;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>
