# Docker Setup for Laravel SQL Logger

This directory contains the Docker configuration for developing and testing the Laravel SQL Logger package.

## Directory Structure

```
docker/
├── Dockerfile              # PHP 8.5 CLI image with Composer and PCOV
├── php/
│   └── local.ini          # PHP configuration
├── scripts/
│   ├── docker-setup.sh    # Initial setup script
│   └── docker-check.sh    # Check Docker environment
└── README.md              # This file
```

## Requirements

- Docker
- Docker Compose

## Quick Start

From the project root:

```bash
# Check Docker environment
chmod +x docker/scripts/docker-check.sh
./docker/scripts/docker-check.sh

# Run initial setup
make setup

# Run tests
make test
```

## What's Included

- **PHP 8.5 CLI** - Latest PHP version
- **Composer** - Dependency management
- **PCOV** - Code coverage extension for PHPUnit
- **Git** - Version control (needed for Composer)

## What's NOT Included

This is a minimal setup for a package (not an application):
- No web server (Nginx/Apache)
- No database (MySQL/PostgreSQL)
- No Node.js/NPM
- No Supervisor

The package is tested against Laravel's database events, but doesn't need a running database for unit tests.
