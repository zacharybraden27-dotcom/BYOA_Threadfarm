#!/bin/bash

# Navigate to the threadfarm directory
cd "$(dirname "$0")"

echo "Starting Threadfarm Application..."
echo "=================================="
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "❌ Error: .env file not found!"
    echo "Please create a .env file from .env.example and set your GEMINI_API_KEY"
    exit 1
fi

# Check if APP_KEY is set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "⚠️  Warning: APP_KEY not set. Generating one..."
    php artisan key:generate
fi

# Clear config cache
echo "Clearing config cache..."
php artisan config:clear

# Check if migrations have been run
if [ ! -f database/database.sqlite ]; then
    echo "Creating database..."
    touch database/database.sqlite
fi

echo "Running migrations..."
php artisan migrate --force

echo ""
echo "✅ Setup complete!"
echo ""
echo "To start the application, run these commands in separate terminals:"
echo ""
echo "Terminal 1 (Laravel server):"
echo "  cd $(pwd)"
echo "  php artisan serve"
echo ""
echo "Terminal 2 (Vite dev server):"
echo "  cd $(pwd)"
echo "  npm run dev"
echo ""
echo "Then visit: http://localhost:8000"
echo ""

