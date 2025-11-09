<?php

/**
 * Subscription Setup Verification Script
 * Run this script to verify your Stripe subscription setup is correct
 * 
 * Usage: php verify-subscription-setup.php
 */

require __DIR__ . '/vendor/autoload.php';

$errors = [];
$warnings = [];
$success = [];

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $envFile = file_get_contents(__DIR__ . '/.env');
    $lines = explode("\n", $envFile);
    $env = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || empty(trim($line))) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
    }
} else {
    $errors[] = ".env file not found";
    $env = [];
}

// Check Stripe configuration
echo "🔍 Checking Stripe Configuration...\n\n";

// Check STRIPE_KEY
if (empty($env['STRIPE_KEY'] ?? '')) {
    $errors[] = "STRIPE_KEY is not set in .env file";
} else {
    $key = $env['STRIPE_KEY'];
    if (strpos($key, 'pk_test_') === 0) {
        $success[] = "STRIPE_KEY is set (Test mode)";
    } elseif (strpos($key, 'pk_live_') === 0) {
        $success[] = "STRIPE_KEY is set (Live mode)";
        $warnings[] = "You are using LIVE mode keys. Make sure this is intentional!";
    } else {
        $errors[] = "STRIPE_KEY format appears invalid (should start with pk_test_ or pk_live_)";
    }
}

// Check STRIPE_SECRET
if (empty($env['STRIPE_SECRET'] ?? '')) {
    $errors[] = "STRIPE_SECRET is not set in .env file";
} else {
    $secret = $env['STRIPE_SECRET'];
    if (strpos($secret, 'sk_test_') === 0) {
        $success[] = "STRIPE_SECRET is set (Test mode)";
    } elseif (strpos($secret, 'sk_live_') === 0) {
        $success[] = "STRIPE_SECRET is set (Live mode)";
        $warnings[] = "You are using LIVE mode keys. Make sure this is intentional!";
    } else {
        $errors[] = "STRIPE_SECRET format appears invalid (should start with sk_test_ or sk_live_)";
    }
}

// Check STRIPE_PRICE_ID
if (empty($env['STRIPE_PRICE_ID'] ?? '')) {
    $errors[] = "STRIPE_PRICE_ID is not set in .env file";
} else {
    $priceId = $env['STRIPE_PRICE_ID'];
    if (strpos($priceId, 'price_') === 0) {
        $success[] = "STRIPE_PRICE_ID is set: $priceId";
        
        // Try to verify the price exists in Stripe
        if (!empty($secret) && strpos($secret, 'sk_') === 0) {
            try {
                \Stripe\Stripe::setApiKey($secret);
                $price = \Stripe\Price::retrieve($priceId);
                $amount = $price->unit_amount / 100;
                $currency = strtoupper($price->currency);
                $interval = $price->recurring->interval ?? 'one-time';
                $success[] = "✅ Price verified: \${$amount} {$currency} per {$interval}";
            } catch (\Exception $e) {
                $warnings[] = "Could not verify price in Stripe: " . $e->getMessage();
            }
        }
    } else {
        $errors[] = "STRIPE_PRICE_ID format appears invalid (should start with price_)";
    }
}

// Check STRIPE_WEBHOOK_SECRET
if (empty($env['STRIPE_WEBHOOK_SECRET'] ?? '')) {
    $warnings[] = "STRIPE_WEBHOOK_SECRET is not set. Webhooks will not work properly.";
    $warnings[] = "For local development, use: stripe listen --forward-to localhost:8000/cashier/webhook";
} else {
    $webhookSecret = $env['STRIPE_WEBHOOK_SECRET'];
    if (strpos($webhookSecret, 'whsec_') === 0) {
        $success[] = "STRIPE_WEBHOOK_SECRET is set";
    } else {
        $warnings[] = "STRIPE_WEBHOOK_SECRET format may be invalid (should start with whsec_)";
    }
}

// Check database configuration
echo "\n🔍 Checking Database Configuration...\n\n";

if (empty($env['DB_CONNECTION'] ?? '')) {
    $warnings[] = "DB_CONNECTION not set in .env (will use default: sqlite)";
} else {
    $success[] = "DB_CONNECTION is set to: " . $env['DB_CONNECTION'];
}

// Check if database file exists (for SQLite)
if (($env['DB_CONNECTION'] ?? 'sqlite') === 'sqlite') {
    $dbPath = $env['DB_DATABASE'] ?? __DIR__ . '/database/database.sqlite';
    if (file_exists($dbPath)) {
        $success[] = "SQLite database file exists: $dbPath";
    } else {
        $warnings[] = "SQLite database file not found: $dbPath";
        $warnings[] = "Run: php artisan migrate to create the database";
    }
}

// Check if migrations have been run
echo "\n🔍 Checking Database Migrations...\n\n";

try {
    // Try to load Laravel to check migrations
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Check if subscription tables exist
    $tables = ['users', 'subscriptions', 'subscription_items'];
    foreach ($tables as $table) {
        try {
            $exists = \Illuminate\Support\Facades\Schema::hasTable($table);
            if ($exists) {
                $success[] = "✅ Table '$table' exists";
            } else {
                $errors[] = "❌ Table '$table' does not exist. Run: php artisan migrate";
            }
        } catch (\Exception $e) {
            $warnings[] = "Could not check table '$table': " . $e->getMessage();
        }
    }
    
    // Check if users table has subscription columns
    try {
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('users');
        $requiredColumns = ['stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at'];
        foreach ($requiredColumns as $column) {
            if (in_array($column, $columns)) {
                $success[] = "✅ Users table has column '$column'";
            } else {
                $errors[] = "❌ Users table missing column '$column'. Run: php artisan migrate";
            }
        }
    } catch (\Exception $e) {
        $warnings[] = "Could not check users table columns: " . $e->getMessage();
    }
} catch (\Exception $e) {
    $warnings[] = "Could not check database: " . $e->getMessage();
    $warnings[] = "Make sure you've run: composer install and php artisan migrate";
}

// Check routes
echo "\n🔍 Checking Routes...\n\n";

$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    $routesContent = file_get_contents($routesFile);
    
    $requiredRoutes = [
        'subscription.index',
        'subscription.checkout',
        'subscription.success',
        'subscription.billing-portal',
    ];
    
    foreach ($requiredRoutes as $route) {
        if (strpos($routesContent, $route) !== false) {
            $success[] = "✅ Route '$route' is defined";
        } else {
            $errors[] = "❌ Route '$route' is missing";
        }
    }
}

// Check middleware
echo "\n🔍 Checking Middleware...\n\n";

$middlewareFile = __DIR__ . '/bootstrap/app.php';
if (file_exists($middlewareFile)) {
    $middlewareContent = file_get_contents($middlewareFile);
    if (strpos($middlewareContent, 'EnsureUserIsSubscribed') !== false) {
        $success[] = "✅ Subscription middleware is registered";
    } else {
        $errors[] = "❌ Subscription middleware is not registered";
    }
}

// Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "📋 VERIFICATION SUMMARY\n";
echo str_repeat("=", 60) . "\n\n";

if (!empty($success)) {
    echo "✅ SUCCESS:\n";
    foreach ($success as $msg) {
        echo "   $msg\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  WARNINGS:\n";
    foreach ($warnings as $msg) {
        echo "   $msg\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ ERRORS:\n";
    foreach ($errors as $msg) {
        echo "   $msg\n";
    }
    echo "\n";
    echo "⚠️  Please fix the errors above before proceeding.\n";
    exit(1);
}

if (empty($errors) && empty($warnings)) {
    echo "🎉 All checks passed! Your subscription setup looks good.\n\n";
    echo "📝 Next Steps:\n";
    echo "   1. Start your development server: php artisan serve\n";
    echo "   2. Set up webhooks for local development:\n";
    echo "      stripe listen --forward-to localhost:8000/cashier/webhook\n";
    echo "   3. Test the subscription flow:\n";
    echo "      - Register a new account\n";
    echo "      - Go to /subscribe\n";
    echo "      - Click 'Start 7-Day Free Trial'\n";
    echo "      - Use test card: 4242 4242 4242 4242\n";
    echo "   4. Verify webhooks are working in Stripe Dashboard\n\n";
} elseif (empty($errors)) {
    echo "✅ Setup looks good, but review the warnings above.\n\n";
}

exit(0);

