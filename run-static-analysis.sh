#!/bin/bash

set -e

echo "================================"
echo "Static Analysis Suite"
echo "================================"
echo ""

# Check if composer dependencies are installed
if [ ! -d "vendor" ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction
    echo ""
fi

# Run PHPStan
echo "🔍 Running PHPStan..."
vendor/bin/phpstan analyse app --level=max --no-progress || true
echo ""

# Run Pest security tests
echo "🛡️  Running security tests..."
php ./vendor/bin/pest tests/Security/ --no-interaction || true
echo ""

# Run Pint (code style)
echo "🎨 Checking code style with Pint..."
vendor/bin/pint --test || true
echo ""

# Check for dependencies vulnerabilities
echo "🔐 Checking for vulnerable dependencies..."
composer audit || true
echo ""

echo "================================"
echo "✅ Static analysis complete!"
echo "================================"
echo ""
echo "Review the output above for any issues."
echo "For more information, see: docs/static-analysis.md"
