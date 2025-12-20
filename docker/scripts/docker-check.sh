#!/bin/bash

set -e

echo "🔍 Checking Docker environment..."
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed"
    exit 1
fi
echo "✅ Docker is installed"

# Check if Docker Compose is installed
if ! command -v docker compose &> /dev/null; then
    echo "❌ Docker Compose is not installed"
    exit 1
fi
echo "✅ Docker Compose is installed"

# Check if Docker daemon is running
if ! docker info &> /dev/null; then
    echo "❌ Docker daemon is not running"
    exit 1
fi
echo "✅ Docker daemon is running"

echo ""
echo "✅ Docker environment is ready!"
