#!/bin/bash

echo "Threadfarm Setup Verification"
echo "=============================="
echo ""

# Check if we're in the right directory
if [ ! -f "package.json" ]; then
    echo "❌ ERROR: package.json not found!"
    echo "   You are in: $(pwd)"
    echo "   Please run this script from the 'threadfarm' directory"
    echo "   Run: cd threadfarm"
    exit 1
fi

echo "✅ Current directory: $(pwd)"
echo "✅ package.json found"
echo ""

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo "⚠️  WARNING: node_modules not found"
    echo "   Run: npm install"
else
    echo "✅ node_modules directory exists"
fi

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "⚠️  WARNING: .env file not found"
    echo "   Run: cp .env.example .env && php artisan key:generate"
else
    echo "✅ .env file exists"
    
    # Check if APP_KEY is set
    if grep -q "APP_KEY=base64:" .env; then
        echo "✅ APP_KEY is set"
    else
        echo "⚠️  WARNING: APP_KEY not set"
        echo "   Run: php artisan key:generate"
    fi
    
    # Check if GEMINI_API_KEY is set
    if grep -q "GEMINI_API_KEY=" .env && ! grep -q "GEMINI_API_KEY=$" .env; then
        echo "✅ GEMINI_API_KEY is set"
    else
        echo "⚠️  WARNING: GEMINI_API_KEY not set"
        echo "   Add your Gemini API key to .env file"
    fi
fi

# Check if database exists
if [ ! -f "database/database.sqlite" ]; then
    echo "⚠️  WARNING: database.sqlite not found"
    echo "   Run: touch database/database.sqlite && php artisan migrate"
else
    echo "✅ database.sqlite exists"
fi

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo "⚠️  WARNING: vendor directory not found"
    echo "   Run: composer install"
else
    echo "✅ vendor directory exists"
fi

echo ""
echo "=============================="
echo "Setup Check Complete!"
echo ""
echo "To start the application:"
echo "  Terminal 1: php artisan serve"
echo "  Terminal 2: npm run dev"
echo ""

