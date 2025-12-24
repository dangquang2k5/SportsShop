# 🚀 Quick Reference Guide

## Start Services

```bash
# Windows
start.bat

# Linux/Mac
./start.sh

# Or manually
docker-compose up -d
```

## Access Points

| Service | URL | Credentials |
|---------|-----|-------------|
| Backend API | http://localhost:3000 | - |
| Frontend | http://localhost:8081 | - |
| MySQL | localhost:3340 | root/030705 |

## Example Pages

| Page | URL | Description |
|------|-----|-------------|
| Login | http://localhost:8081/pages/login_new.php | JWT authentication demo |
| Products | http://localhost:8081/pages/products_new.php | List with filters |
| Checkout | http://localhost:8081/pages/checkout_new.php | Guest checkout demo |

## Default Users

```
Admin:
  Phone: 0123456789
  Password: password

Customer:
  Phone: 0987654321
  Password: password
```

## Common Commands

```bash
# View logs
docker-compose logs -f backend
docker-compose logs -f frontend

# Restart service
docker-compose restart backend

# Stop all
docker-compose down

# Rebuild
docker-compose up -d --build
```

## API Quick Test

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

## Frontend Migration Pattern

```javascript
// 1. Include API client
<script src="../assets/js/api-client.js"></script>

// 2. Check auth
if (!api.isLoggedIn()) {
    window.location.href = 'login.php';
}

// 3. Call API
const response = await api.getProducts();
const products = response.data.products;

// 4. Display data
displayProducts(products);
```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Services won't start | `docker-compose down && docker-compose up -d --build` |
| Backend can't connect to DB | `docker-compose restart backend mysql` |
| CORS errors | Check backend is running on port 3000 |
| 401 Unauthorized | Token expired, login again |

## File Structure

```
backend/
├── src/
│   ├── controllers/    # Business logic
│   ├── routes/         # API endpoints
│   ├── middleware/     # Auth, validation
│   └── server.js       # Entry point
└── .env               # Configuration

frontend/
├── assets/js/
│   └── api-client.js  # API helper
└── pages/
    ├── login_new.php      # Example
    ├── products_new.php   # Example
    └── checkout_new.php   # Example
```

## Next Steps

1. ✅ Run `start.bat` or `start.sh`
2. ✅ Test example pages
3. 📝 Migrate remaining pages (see FRONTEND_MIGRATION_GUIDE.md)
4. 🧪 Test all workflows
5. 🚀 Deploy

## Documentation

- [Main README](README.md)
- [Backend API Docs](backend/README.md)
- [Frontend Migration Guide](FRONTEND_MIGRATION_GUIDE.md)
- [Walkthrough](walkthrough.md)

---

**Need help?** Check the detailed guides above or review the example pages.
