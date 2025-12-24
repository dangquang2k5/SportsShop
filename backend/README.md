# SportShop Backend API

Backend API cho hệ thống thương mại điện tử SportShop, được xây dựng với Node.js, Express, và MySQL.

## 🚀 Công nghệ sử dụng

- **Node.js** 20.x
- **Express** 4.x - Web framework
- **MySQL** 8.0 - Database
- **JWT** - Authentication
- **Bcrypt** - Password hashing
- **Express Validator** - Input validation
- **Helmet** - Security headers
- **CORS** - Cross-origin resource sharing

## 📁 Cấu trúc thư mục

```
backend/
├── src/
│   ├── config/
│   │   └── database.js          # MySQL connection pool
│   ├── controllers/
│   │   ├── auth.controller.js   # Authentication logic
│   │   ├── user.controller.js   # User management
│   │   ├── product.controller.js # Product CRUD
│   │   ├── order.controller.js  # Order processing
│   │   ├── voucher.controller.js # Voucher management
│   │   └── category.controller.js # Categories & Brands
│   ├── middleware/
│   │   ├── auth.js              # JWT verification
│   │   ├── errorHandler.js      # Global error handling
│   │   └── validator.js         # Validation middleware
│   ├── routes/
│   │   ├── auth.routes.js
│   │   ├── user.routes.js
│   │   ├── product.routes.js
│   │   ├── order.routes.js
│   │   ├── voucher.routes.js
│   │   └── category.routes.js
│   └── server.js                # Entry point
├── .env                         # Environment variables
├── .env.example                 # Environment template
├── package.json
└── Dockerfile
```

## 🔧 Cài đặt

### 1. Cài đặt dependencies

```bash
cd backend
npm install
```

### 2. Cấu hình môi trường

Copy file `.env.example` thành `.env` và cập nhật các giá trị:

```env
PORT=3000
NODE_ENV=development
DB_HOST=localhost
DB_PORT=3306
DB_NAME=SportsStoreDB
DB_USER=root
DB_PASSWORD=your_password
JWT_SECRET=your-secret-key
JWT_EXPIRES_IN=24h
FRONTEND_URL=http://localhost:8081
```

### 3. Chạy server

**Development mode:**
```bash
npm run dev
```

**Production mode:**
```bash
npm start
```

Server sẽ chạy tại `http://localhost:3000`

## 🐳 Docker

### Chạy với Docker Compose (Recommended)

Từ thư mục gốc project:

```bash
docker-compose up -d
```

Services sẽ khởi động:
- MySQL: `localhost:3340`
- Backend: `localhost:3000`
- Frontend: `localhost:8081`

### Build riêng backend

```bash
cd backend
docker build -t sportshop-backend .
docker run -p 3000:3000 --env-file .env sportshop-backend
```

## 📡 API Endpoints

### Authentication
- `POST /api/auth/register` - Đăng ký tài khoản
- `POST /api/auth/login` - Đăng nhập
- `GET /api/auth/me` - Lấy thông tin user hiện tại (Protected)

### Users
- `GET /api/users/profile` - Lấy profile (Protected)
- `PUT /api/users/profile` - Cập nhật profile (Protected)
- `PUT /api/users/password` - Đổi mật khẩu (Protected)
- `GET /api/users` - Danh sách users (Admin)
- `PUT /api/users/:id/status` - Khóa/mở khóa user (Admin)
- `DELETE /api/users/:id` - Xóa user (Admin)

### Products
- `GET /api/products` - Danh sách sản phẩm (Public)
- `GET /api/products/:id` - Chi tiết sản phẩm (Public)
- `GET /api/products/:id/variants` - Variants của sản phẩm (Public)
- `POST /api/products` - Tạo sản phẩm (Admin)
- `PUT /api/products/:id` - Cập nhật sản phẩm (Admin)
- `DELETE /api/products/:id` - Xóa sản phẩm (Admin)

### Orders
- `POST /api/orders` - Tạo đơn hàng (Public - supports guest checkout)
- `GET /api/orders` - Danh sách đơn hàng của user (Protected)
- `GET /api/orders/:id` - Chi tiết đơn hàng (Protected)
- `GET /api/orders/admin/all` - Tất cả đơn hàng (Admin)
- `PUT /api/orders/admin/:id/status` - Cập nhật trạng thái (Admin)

### Vouchers
- `GET /api/vouchers` - Danh sách voucher khả dụng (Public)
- `POST /api/vouchers/validate` - Validate mã voucher (Public)
- `GET /api/vouchers/admin/all` - Tất cả vouchers (Admin)
- `POST /api/vouchers/admin` - Tạo voucher (Admin)
- `PUT /api/vouchers/admin/:id` - Cập nhật voucher (Admin)
- `DELETE /api/vouchers/admin/:id` - Xóa voucher (Admin)

### Categories & Brands
- `GET /api/categories` - Danh sách danh mục (Public)
- `GET /api/brands` - Danh sách thương hiệu (Public)
- `POST /api/admin/categories` - Tạo danh mục (Admin)
- `PUT /api/admin/categories/:id` - Cập nhật danh mục (Admin)
- `DELETE /api/admin/categories/:id` - Xóa danh mục (Admin)
- `POST /api/admin/brands` - Tạo thương hiệu (Admin)
- `PUT /api/admin/brands/:id` - Cập nhật thương hiệu (Admin)
- `DELETE /api/admin/brands/:id` - Xóa thương hiệu (Admin)

## 🔐 Authentication

API sử dụng JWT (JSON Web Tokens) cho authentication.

### Đăng nhập

```bash
POST /api/auth/login
Content-Type: application/json

{
  "phone": "0123456789",
  "password": "password123"
}
```

Response:
```json
{
  "success": true,
  "message": "Đăng nhập thành công",
  "data": {
    "userId": 1,
    "firstName": "Nguyen",
    "lastName": "Van A",
    "email": "user@example.com",
    "phone": "0123456789",
    "role": "customer",
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

### Sử dụng token

Thêm token vào header của các requests:

```bash
Authorization: Bearer <your_token_here>
```

## 📝 Examples

### Tạo đơn hàng (Guest Checkout)

```bash
POST /api/orders
Content-Type: application/json

{
  "items": [
    {
      "productDetailId": 1,
      "quantity": 2,
      "price": 2800000
    }
  ],
  "shippingAddress": "123 Nguyen Trai",
  "shippingCity": "TP. Hồ Chí Minh",
  "shippingPhone": "0987654321",
  "guestName": "Nguyen Van B",
  "guestEmail": "guest@example.com",
  "voucherCode": "WELCOME2025",
  "notes": "Giao hàng buổi chiều"
}
```

### Lấy danh sách sản phẩm với filter

```bash
GET /api/products?page=1&limit=12&categoryId=1&minPrice=100000&maxPrice=5000000&sortBy=Price&sortOrder=ASC
```

## 🛡️ Security Features

- **Helmet**: Security headers
- **CORS**: Controlled cross-origin access
- **JWT**: Secure token-based authentication
- **Bcrypt**: Password hashing with salt
- **Input Validation**: Express-validator
- **SQL Injection Prevention**: Parameterized queries
- **Error Handling**: Centralized error handling

## 🔍 Health Check

```bash
GET /health
```

Response:
```json
{
  "success": true,
  "message": "Backend server is running",
  "timestamp": "2024-12-24T04:49:13.000Z"
}
```

## 📊 Database

Backend kết nối với MySQL database `SportsStoreDB`. Schema được tự động khởi tạo khi chạy Docker Compose.

Các bảng chính:
- `Users` - Người dùng
- `Product` - Sản phẩm
- `ProductDetail` - Biến thể sản phẩm
- `Orders` - Đơn hàng
- `OrderDetails` - Chi tiết đơn hàng
- `Voucher` - Mã giảm giá
- `Categories` - Danh mục
- `Brand` - Thương hiệu
- `Reviews` - Đánh giá

## 🐛 Debugging

Xem logs:
```bash
docker-compose logs backend -f
```

Restart backend:
```bash
docker-compose restart backend
```

## 📄 License

ISC
