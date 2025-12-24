<<<<<<< HEAD
# SportShop E-Commerce Platform

Hệ thống thương mại điện tử bán đồ thể thao với kiến trúc phân tách Backend (Node.js/Express) và Frontend (PHP).

## 🏗️ Kiến trúc hệ thống

```
┌─────────────────────────────────────────────────────────┐
│                    Frontend (PHP)                       │
│                   Port: 8081                            │
│  - Presentation Layer                                  │
│  - UI/UX                                               │
│  - API Client (JavaScript)                             │
└────────────────┬────────────────────────────────────────┘
                 │
                 │ HTTP REST API
                 │
┌────────────────▼────────────────────────────────────────┐
│                Backend (Node.js/Express)                │
│                   Port: 3000                            │
│  - Business Logic                                      │
│  - Authentication (JWT)                                │
│  - API Endpoints                                       │
│  - Data Validation                                     │
└────────────────┬────────────────────────────────────────┘
                 │
                 │ MySQL Queries
                 │
┌────────────────▼────────────────────────────────────────┐
│                  MySQL Database                         │
│                   Port: 3340                            │
│  - Data Storage                                        │
│  - Triggers & Stored Procedures                        │
└─────────────────────────────────────────────────────────┘
```

## 🚀 Quick Start

### Prerequisites

- Docker & Docker Compose
- Node.js 20+ (nếu chạy local)
- MySQL 8.0+ (nếu chạy local)

### Chạy với Docker (Recommended)

```bash
# Clone repository
git clone <repository-url>
cd SportsShop-No-backend-anymore-

# Start all services
docker-compose up -d

# Check logs
docker-compose logs -f

# Stop services
docker-compose down
```

Services sẽ khởi động tại:
- **Frontend**: http://localhost:8081
- **Backend API**: http://localhost:3000
- **MySQL**: localhost:3340

### Chạy Development Mode (Local)

**Backend:**
```bash
cd backend
npm install
cp .env.example .env
# Edit .env với database credentials
npm run dev
```

**Frontend:**
```bash
cd frontend
# Setup PHP server hoặc dùng Docker
```

## 📁 Cấu trúc Project

```
SportsShop-No-backend-anymore-/
├── backend/                    # Node.js/Express Backend
│   ├── src/
│   │   ├── config/            # Database config
│   │   ├── controllers/       # Business logic
│   │   ├── middleware/        # Auth, validation, errors
│   │   ├── routes/            # API routes
│   │   └── server.js          # Entry point
│   ├── .env                   # Environment variables
│   ├── Dockerfile
│   ├── package.json
│   └── README.md              # Backend documentation
│
├── frontend/                   # PHP Frontend
│   ├── assets/
│   │   └── js/
│   │       └── api-client.js  # API client helper
│   ├── pages/                 # Application pages
│   ├── includes/              # Shared components
│   ├── config.php             # Configuration
│   └── Dockerfile
│
├── database/
│   └── complete_schema.sql    # Database schema
│
├── docker-compose.yml         # Docker orchestration
└── README.md                  # This file
```

## 🔑 Default Credentials

**Admin:**
- Phone: `0123456789`
- Password: `password`

**Customer:**
- Phone: `0987654321`
- Password: `password`

## 📡 API Documentation

Xem chi tiết tại [backend/README.md](backend/README.md)

### Main Endpoints

- `POST /api/auth/login` - Đăng nhập
- `POST /api/auth/register` - Đăng ký
- `GET /api/products` - Danh sách sản phẩm
- `POST /api/orders` - Tạo đơn hàng
- `GET /api/vouchers` - Mã giảm giá

### Authentication

API sử dụng JWT tokens. Include token trong header:
```
Authorization: Bearer <token>
```

## 🛠️ Tech Stack

### Backend
- **Runtime**: Node.js 20
- **Framework**: Express 4.x
- **Database**: MySQL 8.0
- **Authentication**: JWT + Bcrypt
- **Validation**: Express Validator
- **Security**: Helmet, CORS

### Frontend
- **Language**: PHP 8.2
- **Server**: Apache
- **UI Framework**: Tailwind CSS
- **Icons**: Font Awesome
- **Animations**: Anime.js

### DevOps
- **Containerization**: Docker
- **Orchestration**: Docker Compose
- **Database**: MySQL 8.0

## 🔐 Security Features

- ✅ JWT-based authentication
- ✅ Password hashing (Bcrypt)
- ✅ SQL injection prevention (Parameterized queries)
- ✅ XSS protection
- ✅ CORS configuration
- ✅ Security headers (Helmet)
- ✅ Input validation
- ✅ Role-based access control

## 📊 Database Schema

### Main Tables
- `Users` - User accounts (admin/customer)
- `Product` - Products
- `ProductDetail` - Product variants (size, color)
- `Orders` - Orders (supports guest checkout)
- `OrderDetails` - Order items
- `Voucher` - Discount codes
- `Categories` - Product categories
- `Brand` - Product brands
- `Reviews` - Product reviews

### Features
- Auto-decrement inventory on order
- Auto-calculate product ratings
- Auto-restore inventory on order cancellation
- Stored procedures for complex operations

## 🧪 Testing

### Test Backend API

```bash
# Health check
curl http://localhost:3000/health

# Login
curl -X POST http://localhost:3000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"phone":"0123456789","password":"password"}'

# Get products
curl http://localhost:3000/api/products
```

### Test Frontend

1. Mở browser: http://localhost:8081
2. Login với credentials mặc định
3. Test các workflows:
   - Browse products
   - Add to cart
   - Checkout (guest & member)
   - Admin dashboard

## 📝 Development Workflow

### Adding New API Endpoint

1. Create controller in `backend/src/controllers/`
2. Create route in `backend/src/routes/`
3. Add validation rules
4. Update `server.js` to mount route
5. Update `api-client.js` in frontend
6. Use in frontend pages

### Updating Frontend Page

1. Include `api-client.js`
2. Replace PHP database code with API calls
3. Handle authentication with `api.isLoggedIn()`
4. Handle errors gracefully
5. Add loading states

## 🐛 Troubleshooting

### Backend không kết nối được database

```bash
# Check MySQL container
docker-compose logs mysql

# Restart services
docker-compose restart backend mysql
```

### Frontend không gọi được API

1. Check CORS configuration trong backend
2. Verify BACKEND_URL trong frontend environment
3. Check browser console for errors
4. Verify network connectivity

### Permission errors

```bash
# Fix file permissions
chmod -R 755 backend/
chmod -R 755 frontend/
```

## 📚 Documentation

- [Backend API Documentation](backend/README.md)
- [Implementation Plan](.gemini/antigravity/brain/*/implementation_plan.md)
- [Migration Walkthrough](.gemini/antigravity/brain/*/walkthrough.md)

## 🤝 Contributing

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## 📄 License

ISC

## 👥 Team

Developed by SportShop Team

---

**Note**: Đây là phiên bản đã migrate sang kiến trúc backend riêng biệt. Workflow của admin và user giữ nguyên 100%, chỉ thay đổi cách xử lý dữ liệu bên dưới.
=======
#SportsShop
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
