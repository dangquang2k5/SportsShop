// API Configuration
const API_BASE_URL = 'http://localhost:3000/api';

// API Client Helper
class APIClient {
    constructor() {
        this.baseURL = API_BASE_URL;
    }

    // Get auth token from localStorage
    getToken() {
        return localStorage.getItem('auth_token');
    }

    // Set auth token
    setToken(token) {
        localStorage.setItem('auth_token', token);
    }

    // Remove auth token
    removeToken() {
        localStorage.removeItem('auth_token');
    }

    // Get user data from localStorage
    getUser() {
        const userStr = localStorage.getItem('user_data');
        return userStr ? JSON.parse(userStr) : null;
    }

    // Set user data
    setUser(userData) {
        localStorage.setItem('user_data', JSON.stringify(userData));
    }

    // Remove user data
    removeUser() {
        localStorage.removeItem('user_data');
    }

    // Check if user is logged in
    isLoggedIn() {
        return !!this.getToken();
    }

    // Check if user is admin
    isAdmin() {
        const user = this.getUser();
        return user && user.role === 'admin';
    }

    // Make API request
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const token = this.getToken();

        const headers = {
            'Content-Type': 'application/json',
            ...options.headers
        };

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const config = {
            ...options,
            headers
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'API request failed');
            }

            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // GET request
    async get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    }

    // POST request
    async post(endpoint, body) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(body)
        });
    }

    // PUT request
    async put(endpoint, body) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(body)
        });
    }

    // DELETE request
    async delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }

    // Auth APIs
    async login(phone, password) {
        const response = await this.post('/auth/login', { phone, password });
        if (response.success && response.data.token) {
            this.setToken(response.data.token);
            this.setUser(response.data);
        }
        return response;
    }

    async register(userData) {
        const response = await this.post('/auth/register', userData);
        if (response.success && response.data.token) {
            this.setToken(response.data.token);
            this.setUser(response.data);
        }
        return response;
    }

    async logout() {
        this.removeToken();
        this.removeUser();
    }

    async getMe() {
        return this.get('/auth/me');
    }

    // Product APIs
    async getProducts(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return this.get(`/products${queryString ? '?' + queryString : ''}`);
    }

    async getProduct(id) {
        return this.get(`/products/${id}`);
    }

    async getProductVariants(id) {
        return this.get(`/products/${id}/variants`);
    }

    // Order APIs
    async createOrder(orderData) {
        return this.post('/orders', orderData);
    }

    async getUserOrders() {
        return this.get('/orders');
    }

    async getOrder(id) {
        return this.get(`/orders/${id}`);
    }

    // Voucher APIs
    async getAvailableVouchers() {
        return this.get('/vouchers');
    }

    async validateVoucher(code, orderAmount) {
        return this.post('/vouchers/validate', { code, orderAmount });
    }

    // Category & Brand APIs
    async getCategories() {
        return this.get('/categories');
    }

    async getBrands() {
        return this.get('/brands');
    }

    // User APIs
    async getProfile() {
        return this.get('/users/profile');
    }

    async updateProfile(profileData) {
        return this.put('/users/profile', profileData);
    }

    async changePassword(currentPassword, newPassword) {
        return this.put('/users/password', { currentPassword, newPassword });
    }

    // Admin APIs
    async getAllUsers(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return this.get(`/users${queryString ? '?' + queryString : ''}`);
    }

    async updateUserStatus(userId, status) {
        return this.put(`/users/${userId}/status`, { status });
    }

    async getAllOrders(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return this.get(`/orders/admin/all${queryString ? '?' + queryString : ''}`);
    }

    async updateOrderStatus(orderId, status) {
        return this.put(`/orders/admin/${orderId}/status`, { status });
    }

    async createProduct(productData) {
        return this.post('/products', productData);
    }

    async updateProduct(productId, productData) {
        return this.put(`/products/${productId}`, productData);
    }

    async deleteProduct(productId) {
        return this.delete(`/products/${productId}`);
    }

    async getAllVouchers() {
        return this.get('/vouchers/admin/all');
    }

    async createVoucher(voucherData) {
        return this.post('/vouchers/admin', voucherData);
    }

    async updateVoucher(voucherId, voucherData) {
        return this.put(`/vouchers/admin/${voucherId}`, voucherData);
    }

    async deleteVoucher(voucherId) {
        return this.delete(`/vouchers/admin/${voucherId}`);
    }
}

// Create singleton instance
const api = new APIClient();

// Export for use in other scripts
if (typeof window !== 'undefined') {
    window.api = api;
}
