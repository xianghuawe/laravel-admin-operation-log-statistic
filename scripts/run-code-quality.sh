#!/bin/bash

set -e

echo "===================================="
echo "Running Code Quality Checks"
echo "===================================="

# Check PHP version
echo "\n1. Checking PHP version..."
php --version

# Install dependencies
echo "\n2. Installing dependencies..."
composer install --prefer-dist --no-progress

# Run Laravel Pint
echo "\n3. Running Laravel Pint..."
php vendor/bin/pint --test

# Run PHPStan
echo "\n4. Running PHPStan..."
php vendor/bin/phpstan analyze

# Run PHPUnit
echo "\n5. Running PHPUnit..."
php vendor/bin/phpunit

echo "\n===================================="
echo "Code Quality Checks Completed Successfully!"
echo "===================================="
