@echo off
REM Distributor Management System - Startup Script for Windows

echo 🐳 Starting Distributor Management System...

REM Check if Docker is installed
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker is not installed. Please install Docker Desktop first.
    echo 📖 Visit: https://docs.docker.com/desktop/windows/install/
    pause
    exit /b 1
)

REM Check if Docker Compose is installed
docker-compose --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker Compose is not installed. Please install Docker Compose first.
    pause
    exit /b 1
)

REM Stop any existing containers
echo 🛑 Stopping existing containers...
docker-compose down >nul 2>&1

REM Build and start containers
echo 🔨 Building and starting containers...
docker-compose up -d --build

REM Wait for database to be ready
echo ⏳ Waiting for database to be ready...
timeout /t 10 /nobreak >nul

REM Check if containers are running
echo 🔍 Checking container status...
docker-compose ps

REM Show access information
echo.
echo 🎉 Distributor Management System is ready!
echo.
echo 📱 Access Information:
echo    Web Application: http://localhost:8080
echo    Database Admin:   http://localhost:8081
echo    Database Host:    localhost:3307
echo    Database User:    distributor_user
echo    Database Pass:    distributor_pass
echo.
echo 📝 Useful Commands:
echo    View logs:       docker-compose logs -f
echo    Stop system:     docker-compose down
echo    Restart system:  docker-compose restart
echo.
echo 🔧 Troubleshooting:
echo    If ports 8080/8081 are occupied, edit docker-compose.yml
echo    Check logs with: docker-compose logs web
echo.
pause
