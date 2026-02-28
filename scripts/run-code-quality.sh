#!/bin/bash

set -e

echo "===================================="
echo "Running Code Quality Checks"
echo "===================================="

# Check PHP version
echo "1. Checking PHP version..."
php --version

# Install dependencies
echo "2. Installing dependencies..."
composer install --prefer-dist --no-progress

# Run Laravel Pint
echo "3. Running Laravel Pint..."
php vendor/bin/pint --test

# Run PHPStan
echo "4. Running PHPStan..."
php vendor/bin/phpstan analyze

echo "===================================="
echo "Code Quality Checks Completed Successfully!"
echo "===================================="
