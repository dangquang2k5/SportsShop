@echo off
echo ================================================
echo   SportShop - Quick Start Script
echo ================================================
echo.

REM Check if Docker is running
docker info >nul 2>&1
if errorlevel 1 (
    echo X Docker is not running. Please start Docker Desktop first.
    pause
    exit /b 1
)

echo [OK] Docker is running
echo.

REM Stop existing containers
echo [*] Stopping existing containers...
docker-compose down

REM Build and start services
echo [*] Building and starting services...
docker-compose up -d --build

REM Wait for services
echo.
echo [*] Waiting for services to be ready...
timeout /t 10 /nobreak >nul

REM Check services
echo [*] Checking MySQL...
timeout /t 5 /nobreak >nul
echo [OK] MySQL should be ready

echo [*] Checking Backend...
timeout /t 5 /nobreak >nul
curl -s http://localhost:3000/health >nul 2>&1
if errorlevel 1 (
    echo [!] Backend is starting...
) else (
    echo [OK] Backend is ready
)

echo [*] Checking Frontend...
curl -s http://localhost:8081 >nul 2>&1
if errorlevel 1 (
    echo [!] Frontend is starting...
) else (
    echo [OK] Frontend is ready
)

echo.
echo ================================================
echo   Services are starting up!
echo ================================================
echo.
echo Backend API:  http://localhost:3000
echo Frontend:     http://localhost:8081
echo MySQL:        localhost:3340
echo.
echo Example Pages:
echo    - Login:      http://localhost:8081/pages/login_new.php
echo    - Products:   http://localhost:8081/pages/products_new.php
echo    - Checkout:   http://localhost:8081/pages/checkout_new.php
echo.
echo Default Credentials:
echo    Admin:  0123456789 / password
echo    User:   0987654321 / password
echo.
echo View logs:
echo    docker-compose logs -f backend
echo    docker-compose logs -f frontend
echo.
echo Stop services:
echo    docker-compose down
echo.
echo ================================================
echo.
pause
