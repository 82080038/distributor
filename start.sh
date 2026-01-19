#!/bin/bash

# Distributor Management System - Startup Script
# Cross-platform deployment script

echo "🐳 Starting Distributor Management System..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    echo "📖 Visit: https://docs.docker.com/get-docker/"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Stop any existing containers
echo "🛑 Stopping existing containers..."
docker-compose down 2>/dev/null

# Build and start containers
echo "🔨 Building and starting containers..."
docker-compose up -d --build

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
sleep 10

# Check if containers are running
echo "🔍 Checking container status..."
docker-compose ps

# Show access information
echo ""
echo "🎉 Distributor Management System is ready!"
echo ""
echo "📱 Access Information:"
echo "   Web Application: http://localhost:8080"
echo "   Database Admin:   http://localhost:8081"
echo "   Database Host:    localhost:3307"
echo "   Database User:    distributor_user"
echo "   Database Pass:    distributor_pass"
echo ""
echo "📝 Useful Commands:"
echo "   View logs:       docker-compose logs -f"
echo "   Stop system:     docker-compose down"
echo "   Restart system:  docker-compose restart"
echo ""
echo "🔧 Troubleshooting:"
echo "   If ports 8080/8081 are occupied, edit docker-compose.yml"
echo "   Check logs with: docker-compose logs web"
echo ""
