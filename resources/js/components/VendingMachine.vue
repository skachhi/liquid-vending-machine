<template>
    <div class="container">
        <div class="card">
            <h1 class="text-center mb-20">
                <i class="fas fa-coffee"></i> Liquid Vending Machine
            </h1>
            
            <!-- Machine Status -->
            <div class="machine-status mb-20">
                <div class="status-indicators">
                    <div class="status-item" :class="{ active: machineStatus.online }">
                        <i class="fas fa-power-off"></i>
                        <span>{{ machineStatus.online ? 'Online' : 'Offline' }}</span>
                    </div>
                    <div class="status-item" :class="{ active: machineStatus.stockAvailable }">
                        <i class="fas fa-boxes"></i>
                        <span>{{ machineStatus.stockAvailable ? 'Stock Available' : 'Low Stock' }}</span>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="products-section">
                <h2 class="mb-20">Select Your Drink</h2>
                <div class="grid grid-4" v-if="!loading">
                    <div 
                        v-for="product in products" 
                        :key="product.id"
                        class="product-card"
                        :class="{ disabled: product.stock <= 0 }"
                        @click="selectProduct(product)"
                    >
                        <div class="product-image">
                            <i :class="getProductIcon(product.category)"></i>
                        </div>
                        <div class="product-info">
                            <h3>{{ product.name }}</h3>
                            <p class="price">₹{{ product.price }}</p>
                            <p class="stock">Stock: {{ product.stock }}</p>
                        </div>
                    </div>
                </div>
                
                <div v-else class="loading">
                    <div class="spinner"></div>
                </div>
            </div>

            <!-- Selected Product -->
            <div v-if="selectedProduct" class="selected-product card mt-20">
                <h3>Selected: {{ selectedProduct.name }}</h3>
                <div class="product-details">
                    <div class="product-image-large">
                        <i :class="getProductIcon(selectedProduct.category)"></i>
                    </div>
                    <div class="product-info-large">
                        <p><strong>Price:</strong> ₹{{ selectedProduct.price }}</p>
                        <p><strong>Category:</strong> {{ selectedProduct.category }}</p>
                        <p><strong>Available:</strong> {{ selectedProduct.stock }} units</p>
                    </div>
                </div>
                
                <!-- Quantity Selection -->
                <div class="quantity-section">
                    <label>Quantity:</label>
                    <div class="quantity-controls">
                        <button @click="decreaseQuantity" :disabled="quantity <= 1" class="btn btn-warning">-</button>
                        <span class="quantity-display">{{ quantity }}</span>
                        <button @click="increaseQuantity" :disabled="quantity >= selectedProduct.stock" class="btn btn-warning">+</button>
                    </div>
                </div>
                
                <!-- Payment Section -->
                <div class="payment-section">
                    <div class="total-price">
                        <h3>Total: ₹{{ totalPrice }}</h3>
                    </div>
                    
                    <div class="payment-methods">
                        <button @click="processPayment('cash')" class="btn btn-primary">
                            <i class="fas fa-money-bill-wave"></i> Cash Payment
                        </button>
                        <button @click="processPayment('card')" class="btn btn-success">
                            <i class="fas fa-credit-card"></i> Card Payment
                        </button>
                        <button @click="processPayment('upi')" class="btn btn-warning">
                            <i class="fas fa-mobile-alt"></i> UPI Payment
                        </button>
                    </div>
                </div>
            </div>

            <!-- Admin Access -->
            <div class="admin-access text-center mt-20">
                <router-link to="/admin" class="btn btn-danger">
                    <i class="fas fa-cog"></i> Admin Panel
                </router-link>
            </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="showPaymentModal" class="modal-overlay" @click="closePaymentModal">
            <div class="modal-content" @click.stop>
                <h3>Processing Payment...</h3>
                <div class="payment-processing">
                    <div class="spinner"></div>
                    <p>{{ paymentMessage }}</p>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div v-if="showSuccessModal" class="modal-overlay" @click="closeSuccessModal">
            <div class="modal-content success-modal" @click.stop>
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Order Successful!</h3>
                <p>Your {{ selectedProduct?.name }} is being prepared...</p>
                <button @click="closeSuccessModal" class="btn btn-primary">Close</button>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

export default {
    name: 'VendingMachine',
    setup() {
        const products = ref([])
        const selectedProduct = ref(null)
        const quantity = ref(1)
        const loading = ref(true)
        const showPaymentModal = ref(false)
        const showSuccessModal = ref(false)
        const paymentMessage = ref('')
        const machineStatus = ref({
            online: true,
            stockAvailable: true
        })

        const totalPrice = computed(() => {
            return selectedProduct.value ? selectedProduct.value.price * quantity.value : 0
        })

        const getProductIcon = (category) => {
            const icons = {
                'Coffee': 'fas fa-coffee',
                'Tea': 'fas fa-mug-hot',
                'Juice': 'fas fa-glass-martini-alt',
                'Water': 'fas fa-tint',
                'Soda': 'fas fa-wine-bottle',
                'Energy Drink': 'fas fa-bolt'
            }
            return icons[category] || 'fas fa-glass'
        }

        const fetchProducts = async () => {
            try {
                const response = await axios.get('/products')
                products.value = response.data
                loading.value = false
            } catch (error) {
                console.error('Error fetching products:', error)
                loading.value = false
            }
        }

        const selectProduct = (product) => {
            if (product.stock > 0) {
                selectedProduct.value = product
                quantity.value = 1
            }
        }

        const increaseQuantity = () => {
            if (quantity.value < selectedProduct.value.stock) {
                quantity.value++
            }
        }

        const decreaseQuantity = () => {
            if (quantity.value > 1) {
                quantity.value--
            }
        }

        const processPayment = async (method) => {
            if (!selectedProduct.value) return

            showPaymentModal.value = true
            paymentMessage.value = `Processing ${method} payment...`

            try {
                // Simulate payment processing
                await new Promise(resolve => setTimeout(resolve, 2000))

                const orderData = {
                    product_id: selectedProduct.value.id,
                    quantity: quantity.value,
                    payment_method: method
                }

                const response = await axios.post('/orders', orderData)
                
                if (response.data.success) {
                    // Update product stock
                    selectedProduct.value.stock -= quantity.value
                    
                    // Update products array
                    const productIndex = products.value.findIndex(p => p.id === selectedProduct.value.id)
                    if (productIndex !== -1) {
                        products.value[productIndex].stock = selectedProduct.value.stock
                    }

                    showPaymentModal.value = false
                    showSuccessModal.value = true
                }
            } catch (error) {
                console.error('Payment error:', error)
                paymentMessage.value = 'Payment failed. Please try again.'
                setTimeout(() => {
                    showPaymentModal.value = false
                }, 2000)
            }
        }

        const closePaymentModal = () => {
            showPaymentModal.value = false
        }

        const closeSuccessModal = () => {
            showSuccessModal.value = false
            selectedProduct.value = null
            quantity.value = 1
        }

        onMounted(() => {
            fetchProducts()
        })

        return {
            products,
            selectedProduct,
            quantity,
            loading,
            showPaymentModal,
            showSuccessModal,
            paymentMessage,
            machineStatus,
            totalPrice,
            getProductIcon,
            selectProduct,
            increaseQuantity,
            decreaseQuantity,
            processPayment,
            closePaymentModal,
            closeSuccessModal
        }
    }
}
</script>

<style scoped>
.machine-status {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.status-indicators {
    display: flex;
    gap: 30px;
    justify-content: center;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    border-radius: 25px;
    background: #e9ecef;
    color: #6c757d;
    transition: all 0.3s ease;
}

.status-item.active {
    background: #d4edda;
    color: #155724;
}

.status-item i {
    font-size: 18px;
}

.product-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.product-card:hover {
    transform: translateY(-5px);
    border-color: #667eea;
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
}

.product-card.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.product-image {
    font-size: 48px;
    color: #667eea;
    margin-bottom: 15px;
}

.product-info h3 {
    margin-bottom: 10px;
    color: #333;
}

.price {
    font-size: 18px;
    font-weight: bold;
    color: #28a745;
    margin-bottom: 5px;
}

.stock {
    font-size: 14px;
    color: #6c757d;
}

.selected-product {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.product-details {
    display: flex;
    align-items: center;
    gap: 30px;
    margin: 20px 0;
}

.product-image-large {
    font-size: 64px;
    color: #667eea;
}

.product-info-large {
    flex: 1;
}

.product-info-large p {
    margin-bottom: 10px;
    font-size: 16px;
}

.quantity-section {
    margin: 20px 0;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 15px;
    justify-content: center;
    margin-top: 10px;
}

.quantity-display {
    font-size: 24px;
    font-weight: bold;
    min-width: 50px;
    text-align: center;
}

.payment-section {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #e9ecef;
}

.total-price {
    text-align: center;
    margin-bottom: 20px;
}

.total-price h3 {
    font-size: 28px;
    color: #28a745;
}

.payment-methods {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
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
    text-align: center;
    max-width: 400px;
    width: 90%;
}

.payment-processing {
    margin-top: 20px;
}

.success-icon {
    font-size: 64px;
    color: #28a745;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .status-indicators {
        flex-direction: column;
        gap: 15px;
    }
    
    .product-details {
        flex-direction: column;
        text-align: center;
    }
    
    .payment-methods {
        flex-direction: column;
    }
    
    .quantity-controls {
        gap: 10px;
    }
}
</style>
