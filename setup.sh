#!/bin/bash
# LearningPilot.ai - Initial Setup Script
# Run this script in the project root after cloning

set -e

echo "🚀 LearningPilot.ai Setup Starting..."

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
if [[ $(echo "$PHP_VERSION < 8.4" | bc) -eq 1 ]]; then
    echo "❌ PHP 8.4+ required. Current version: $PHP_VERSION"
    exit 1
fi
echo "✅ PHP $PHP_VERSION detected"

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed"
    exit 1
fi
echo "✅ Composer detected"

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed"
    exit 1
fi
echo "✅ npm detected"

# Install composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --prefer-dist --no-interaction

# Install TALL stack packages if not present
if ! composer show livewire/livewire &> /dev/null; then
    echo "📦 Installing Livewire..."
    composer require livewire/livewire
fi

if ! composer show laravel/jetstream &> /dev/null; then
    echo "📦 Installing Jetstream..."
    composer require laravel/jetstream
    php artisan jetstream:install livewire --teams=false
fi

# Additional packages
echo "📦 Installing additional packages..."
composer require barryvdh/laravel-dompdf --no-interaction || true
composer require laravel/scout --no-interaction || true
composer require meilisearch/meilisearch-php http-interop/http-factory-guzzle --no-interaction || true

# Development packages
composer require --dev laravel/pint phpstan/phpstan barryvdh/laravel-ide-helper --no-interaction || true

# Setup environment
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    php artisan key:generate
fi

# Install npm dependencies
echo "📦 Installing npm dependencies..."
npm install

# Build assets
echo "🔨 Building assets..."
npm run build

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link || true

# Generate IDE helpers (optional, for development)
if composer show barryvdh/laravel-ide-helper &> /dev/null; then
    echo "📝 Generating IDE helpers..."
    php artisan ide-helper:generate || true
fi

echo ""
echo "✅ LearningPilot.ai setup complete!"
echo ""
echo "Next steps:"
echo "1. Configure your database in .env"
echo "2. Run: php artisan migrate"
echo "3. Run: php artisan db:seed"
echo "4. Run: php artisan serve"
echo ""
echo "For development with hot reload:"
echo "  Terminal 1: php artisan serve"
echo "  Terminal 2: npm run dev"
