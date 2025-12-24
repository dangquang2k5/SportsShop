#!/bin/bash

echo "================================================"
echo "  SportShop - Quick Start Script"
echo "================================================"
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

echo "✅ Docker is running"
echo ""

# Stop existing containers
echo "🛑 Stopping existing containers..."
docker-compose down

# Build and start services
echo "🚀 Building and starting services..."
docker-compose up -d --build

# Wait for services to be healthy
echo ""
echo "⏳ Waiting for services to be ready..."
sleep 10

# Check MySQL
echo "📊 Checking MySQL..."
docker-compose exec -T mysql mysqladmin ping -h localhost -u root -p030705 > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ MySQL is ready"
else
    echo "⚠️  MySQL is starting... (this may take a moment)"
fi

# Check Backend
echo "🔧 Checking Backend..."
sleep 5
curl -s http://localhost:3000/health > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ Backend is ready"
else
    echo "⚠️  Backend is starting..."
fi

# Check Frontend
echo "🎨 Checking Frontend..."
curl -s http://localhost:8081 > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ Frontend is ready"
else
    echo "⚠️  Frontend is starting..."
fi

echo ""
echo "================================================"
echo "  🎉 Services are starting up!"
echo "================================================"
echo ""
echo "📡 Backend API:  http://localhost:3000"
echo "🌐 Frontend:     http://localhost:8081"
echo "💾 MySQL:        localhost:3340"
echo ""
echo "📄 Example Pages:"
echo "   - Login:      http://localhost:8081/pages/login_new.php"
echo "   - Products:   http://localhost:8081/pages/products_new.php"
echo "   - Checkout:   http://localhost:8081/pages/checkout_new.php"
echo ""
echo "🔑 Default Credentials:"
echo "   Admin:  0123456789 / password"
echo "   User:   0987654321 / password"
echo ""
echo "📋 View logs:"
echo "   docker-compose logs -f backend"
echo "   docker-compose logs -f frontend"
echo ""
echo "🛑 Stop services:"
echo "   docker-compose down"
echo ""
echo "================================================"
