#!/bin/bash

# Database Setup Script for Threadfarm
# This script helps you set up the database for the subscription system

echo "🔧 Threadfarm Database Setup"
echo "============================"
echo ""

# Check if .env file exists
if [ ! -f .env ]; then
    echo "❌ Error: .env file not found!"
    echo "Please create a .env file first."
    exit 1
fi

# Check current database configuration
DB_CONNECTION=$(grep "^DB_CONNECTION=" .env | cut -d '=' -f2 | tr -d '"' | tr -d "'")

echo "Current DB_CONNECTION: $DB_CONNECTION"
echo ""

if [ "$DB_CONNECTION" = "pgsql" ] || [ "$DB_CONNECTION" = "postgresql" ]; then
    echo "⚠️  PostgreSQL is configured but connection is failing."
    echo ""
    echo "Options:"
    echo "1. Fix PostgreSQL connection"
    echo "2. Switch to SQLite (recommended for development)"
    echo ""
    read -p "Choose an option (1 or 2): " choice
    
    if [ "$choice" = "2" ]; then
        echo ""
        echo "Switching to SQLite..."
        
        # Backup current .env
        cp .env .env.backup
        
        # Update DB_CONNECTION to sqlite
        if [[ "$OSTYPE" == "darwin"* ]]; then
            # macOS
            sed -i '' 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
            sed -i '' 's/^DB_DATABASE=.*/DB_DATABASE=database\/database.sqlite/' .env
        else
            # Linux
            sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
            sed -i 's/^DB_DATABASE=.*/DB_DATABASE=database\/database.sqlite/' .env
        fi
        
        echo "✅ Updated .env to use SQLite"
        
        # Create database file if it doesn't exist
        if [ ! -f database/database.sqlite ]; then
            touch database/database.sqlite
            echo "✅ Created database/database.sqlite"
        fi
        
        echo ""
        echo "Now run: php artisan migrate"
    else
        echo ""
        echo "To fix PostgreSQL:"
        echo "1. Make sure PostgreSQL is installed and running"
        echo "2. Create a database: createdb threadfarm"
        echo "3. Update .env with correct credentials:"
        echo "   DB_CONNECTION=pgsql"
        echo "   DB_HOST=127.0.0.1"
        echo "   DB_PORT=5432"
        echo "   DB_DATABASE=threadfarm"
        echo "   DB_USERNAME=your_username"
        echo "   DB_PASSWORD=your_password"
        echo ""
        echo "Then run: php artisan migrate"
    fi
elif [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "✅ SQLite is configured"
    
    # Create database file if it doesn't exist
    if [ ! -f database/database.sqlite ]; then
        touch database/database.sqlite
        echo "✅ Created database/database.sqlite"
    else
        echo "✅ Database file exists"
    fi
    
    echo ""
    echo "Running migrations..."
    php artisan migrate
else
    echo "⚠️  Unknown database connection: $DB_CONNECTION"
    echo "Please update your .env file with DB_CONNECTION=sqlite or DB_CONNECTION=pgsql"
fi

echo ""
echo "✅ Setup complete!"

