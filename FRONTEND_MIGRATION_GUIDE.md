# Frontend Migration Guide

## 📋 Overview

Hướng dẫn chi tiết cách migrate các pages frontend từ PHP database queries sang API calls.

## 🔄 Migration Pattern

### 1. Include API Client

Thêm vào `<head>` của mỗi page:

```html
<script src="../assets/js/api-client.js"></script>
```

### 2. Remove PHP Database Code

**Before (Old):**
```php
<?php
require_once '../config.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM Product");
$stmt->execute();
$products = $stmt->fetchAll();
?>
```

**After (New):**
```javascript
<script>
async function loadProducts() {
    try {
        const response = await api.getProducts();
        const products = response.data.products;
        displayProducts(products);
    } catch (error) {
        console.error('Error:', error);
    }
}
</script>
```

### 3. Authentication Check

**Before (Old):**
```php
<?php
if (!isLoggedIn()) {
    redirect('login.php');
}
?>
```

**After (New):**
```javascript
<script>
if (!api.isLoggedIn()) {
    window.location.href = 'login.php';
}

// Check admin
if (!api.isAdmin()) {
    alert('Bạn không có quyền truy cập');
    window.location.href = '../index.php';
}
</script>
```

## 📄 Page-by-Page Migration

### Authentication Pages

#### `login.php`
✅ **Example created**: `login_new.php`

**Key changes:**
- Remove PHP session code
- Use `api.login(phone, password)`
- Store token in localStorage
- Redirect based on role

```javascript
const response = await api.login(phone, password);
if (response.success) {
    // Token automatically stored
    if (response.data.role === 'admin') {
        window.location.href = 'admin_dashboard.php';
    } else {
        window.location.href = '../index.php';
    }
}
```

#### `register.php`

```javascript
const response = await api.register({
    firstName,
    lastName,
    email,
    phone,
    password,
    address
});
```

#### `logout.php`

```javascript
api.logout();
window.location.href = 'login.php';
```

---

### Product Pages

#### `products.php`
✅ **Example created**: `products_new.php`

**Features:**
- Filter by category, brand
- Search
- Pagination
- Sorting

```javascript
const response = await api.getProducts({
    page: 1,
    limit: 12,
    search: 'nike',
    categoryId: 1,
    brandId: 2,
    sortBy: 'Price',
    sortOrder: 'ASC'
});
```

#### `product_detail.php`

```javascript
const productId = new URLSearchParams(window.location.search).get('id');
const [product, variants] = await Promise.all([
    api.getProduct(productId),
    api.getProductVariants(productId)
]);
```

---

### Cart & Checkout

#### `cart.php`

Cart được lưu trong **localStorage** (client-side):

```javascript
// Get cart
let cart = JSON.parse(localStorage.getItem('cart') || '[]');

// Add to cart
cart.push({
    productDetailId: 1,
    name: 'Product Name',
    price: 100000,
    quantity: 1,
    image: 'image.jpg',
    size: 'M',
    color: 'Red'
});
localStorage.setItem('cart', JSON.stringify(cart));

// Update quantity
cart[index].quantity = newQuantity;
localStorage.setItem('cart', JSON.stringify(cart));

// Remove item
cart.splice(index, 1);
localStorage.setItem('cart', JSON.stringify(cart));
```

#### `checkout.php`
✅ **Example created**: `checkout_new.php`

**Features:**
- Guest checkout support
- Voucher validation
- Order summary
- Real-time total calculation

```javascript
const orderData = {
    items: cart.map(item => ({
        productDetailId: item.productDetailId,
        quantity: item.quantity,
        price: item.price
    })),
    shippingAddress,
    shippingCity,
    shippingPhone,
    guestName,      // For guest checkout
    guestEmail,     // For guest checkout
    voucherCode,
    notes
};

const response = await api.createOrder(orderData);
```

---

### User Profile

#### `profile.php`

```javascript
// Get profile
const response = await api.getProfile();
const user = response.data;

// Update profile
await api.updateProfile({
    firstName,
    lastName,
    email,
    phone,
    address
});

// Change password
await api.changePassword(currentPassword, newPassword);

// Get user orders
const orders = await api.getUserOrders();
```

---

### Admin Pages

#### `admin_dashboard.php`

```javascript
// Check admin permission
if (!api.isAdmin()) {
    window.location.href = '../index.php';
}

// Get statistics (you may need to create custom endpoints)
const [users, orders, products] = await Promise.all([
    api.getAllUsers({ limit: 5 }),
    api.getAllOrders({ limit: 10 }),
    api.getProducts({ limit: 5 })
]);
```

#### `admin_products.php`

```javascript
// List products
const response = await api.getProducts({ page, limit });

// Create product
await api.createProduct({
    productName,
    price,
    description,
    mainImage,
    categoryId,
    brandId,
    status: 'active'
});

// Update product
await api.updateProduct(productId, productData);

// Delete product
await api.deleteProduct(productId);
```

#### `admin_orders.php`

```javascript
// Get all orders
const response = await api.getAllOrders({
    page: 1,
    limit: 20,
    status: 'pending'  // Filter by status
});

// Update order status
await api.updateOrderStatus(orderId, 'shipped');
```

#### `admin_users.php`

```javascript
// Get all users
const response = await api.getAllUsers({
    page: 1,
    limit: 20,
    search: 'keyword'
});

// Update user status (lock/unlock)
await api.updateUserStatus(userId, true);  // true = active, false = locked
```

#### `admin_vouchers.php`

```javascript
// Get all vouchers
const vouchers = await api.getAllVouchers();

// Create voucher
await api.createVoucher({
    voucherCode: 'SUMMER2025',
    discountValue: 100000,
    minOrderValue: 500000,
    quantity: 100,
    startDate: '2025-06-01',
    endDate: '2025-08-31'
});

// Update voucher
await api.updateVoucher(voucherId, voucherData);

// Delete voucher
await api.deleteVoucher(voucherId);
```

---

## 🎨 UI Patterns

### Loading State

```javascript
// Show loading
document.getElementById('loading').classList.remove('hidden');
document.getElementById('content').classList.add('hidden');

try {
    const response = await api.getProducts();
    // Process data
} catch (error) {
    // Handle error
} finally {
    // Hide loading
    document.getElementById('loading').classList.add('hidden');
    document.getElementById('content').classList.remove('hidden');
}
```

### Error Handling

```javascript
try {
    const response = await api.createOrder(orderData);
    // Success
    showSuccess('Đặt hàng thành công!');
} catch (error) {
    // Error
    showError(error.message);
}

function showError(message) {
    const errorDiv = document.getElementById('error-message');
    errorDiv.textContent = message;
    errorDiv.classList.remove('hidden');
}
```

### Form Submission

```javascript
document.getElementById('my-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
    
    try {
        const formData = {
            field1: document.getElementById('field1').value,
            field2: document.getElementById('field2').value
        };
        
        const response = await api.someEndpoint(formData);
        
        if (response.success) {
            alert('Thành công!');
        }
    } catch (error) {
        alert('Lỗi: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Submit';
    }
});
```

---

## ✅ Checklist

Khi migrate một page, đảm bảo:

- [ ] Include `api-client.js`
- [ ] Remove PHP database code
- [ ] Replace with API calls
- [ ] Handle authentication
- [ ] Add loading states
- [ ] Add error handling
- [ ] Test all functionality
- [ ] Check console for errors
- [ ] Verify data displays correctly

---

## 🐛 Common Issues

### CORS Error

**Error:** `Access to fetch at 'http://localhost:3000/api/...' from origin 'http://localhost:8081' has been blocked by CORS policy`

**Solution:** Backend đã cấu hình CORS. Đảm bảo backend đang chạy.

### 401 Unauthorized

**Error:** `401 Unauthorized`

**Solution:** Token hết hạn hoặc không hợp lệ. Đăng nhập lại.

```javascript
if (error.message.includes('401') || error.message.includes('token')) {
    api.logout();
    window.location.href = 'login.php';
}
```

### Empty Response

**Issue:** API trả về data nhưng không hiển thị

**Solution:** Check response structure:

```javascript
console.log('Response:', response);
console.log('Data:', response.data);
```

---

## 📚 Resources

- [Backend API Documentation](../backend/README.md)
- [API Client Source](../frontend/assets/js/api-client.js)
- [Example Pages](../frontend/pages/*_new.php)

---

## 💡 Tips

1. **Use Browser DevTools**: Network tab để xem API calls
2. **Check Backend Logs**: `docker-compose logs backend -f`
3. **Test API First**: Dùng Postman/curl trước khi integrate
4. **Keep Cart Simple**: Dùng localStorage cho cart, đơn giản và nhanh
5. **Handle Errors**: Luôn có try-catch và hiển thị lỗi cho user
