# Subscription Setup Checklist

Use this checklist to ensure everything is configured correctly.

## ✅ Stripe Configuration (Verified)
- [x] STRIPE_KEY is set (Test mode)
- [x] STRIPE_SECRET is set (Test mode)
- [x] STRIPE_PRICE_ID is set and verified ($10 USD per month)
- [x] STRIPE_WEBHOOK_SECRET is set

## ⚠️ Database Setup (Action Required)
Your `.env` file shows `DB_CONNECTION=pgsql`, but the database connection is failing.

**Choose one option:**

### Option 1: Use SQLite (Recommended for Development)
1. Update your `.env` file:
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   ```
2. Create the database file (if it doesn't exist):
   ```bash
   touch database/database.sqlite
   ```
3. Run migrations:
   ```bash
   php artisan migrate
   ```

### Option 2: Use PostgreSQL
1. Make sure PostgreSQL is installed and running
2. Create a database:
   ```bash
   createdb threadfarm
   ```
3. Update your `.env` file with correct PostgreSQL credentials:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=threadfarm
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```
4. Run migrations:
   ```bash
   php artisan migrate
   ```

## ✅ Routes (Verified)
- [x] Subscription routes are defined
- [x] Middleware is registered

## 🔍 Final Verification Steps

### 1. Run Database Migrations
```bash
php artisan migrate
```

This will create:
- `subscriptions` table
- `subscription_items` table
- Add subscription columns to `users` table

### 2. Verify Database Tables
After running migrations, verify the tables exist:
```bash
php artisan tinker
```
Then in tinker:
```php
Schema::hasTable('subscriptions'); // Should return true
Schema::hasTable('subscription_items'); // Should return true
Schema::hasColumns('users', ['stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at']); // Should return true
```

### 3. Set Up Webhooks (Local Development)

**For local testing, use Stripe CLI:**

1. Install Stripe CLI if you haven't:
   - macOS: `brew install stripe/stripe-cli/stripe`
   - Windows: Download from https://github.com/stripe/stripe-cli/releases
   - Linux: See https://stripe.com/docs/stripe-cli

2. Login to Stripe:
   ```bash
   stripe login
   ```

3. Forward webhooks to your local server:
   ```bash
   stripe listen --forward-to localhost:8000/cashier/webhook
   ```

4. Copy the webhook signing secret (starts with `whsec_`) and update your `.env`:
   ```env
   STRIPE_WEBHOOK_SECRET=whsec_...
   ```

### 4. Test the Subscription Flow

1. **Start your development server:**
   ```bash
   php artisan serve
   ```

2. **Start webhook forwarding** (in a separate terminal):
   ```bash
   stripe listen --forward-to localhost:8000/cashier/webhook
   ```

3. **Test the flow:**
   - Go to http://localhost:8000
   - Register a new account
   - You should be redirected to `/subscribe`
   - Click "Start 7-Day Free Trial"
   - You'll be redirected to Stripe Checkout
   - Use test card: `4242 4242 4242 4242`
   - Use any future expiry date (e.g., 12/34)
   - Use any CVC (e.g., 123)
   - Use any ZIP (e.g., 12345)
   - Complete checkout
   - You should be redirected back to your app with access granted

### 5. Verify Webhooks Are Working

After completing a test checkout, check:
- Stripe CLI terminal should show webhook events
- Your application should receive the webhook and update the subscription
- User should have access to the app

### 6. Test Subscription Management

1. Go to your dashboard
2. Look for a "Manage Subscription" button (if you added it to navigation)
3. Or go to `/subscribe` and click "Manage Subscription"
4. You should be redirected to Stripe Billing Portal
5. Verify you can see your subscription details

## 🚨 Common Issues & Solutions

### Issue: "Subscription price not configured"
**Solution:** Make sure `STRIPE_PRICE_ID` is set in your `.env` file and matches a price in your Stripe Dashboard.

### Issue: Webhooks not working
**Solution:** 
- For local development: Use Stripe CLI to forward webhooks
- Make sure `STRIPE_WEBHOOK_SECRET` is set correctly
- Check that the webhook endpoint URL is correct

### Issue: "Table doesn't exist" errors
**Solution:** Run `php artisan migrate` to create the necessary tables.

### Issue: Users can access app without subscription
**Solution:** 
- Verify the `subscribed` middleware is applied to protected routes
- Check that routes are wrapped in `Route::middleware('subscribed')`

### Issue: Trial not working
**Solution:** 
- Verify the checkout is created with `->trialDays(7)`
- Check that `trial_ends_at` is set in the database after checkout
- Verify the middleware checks `onTrial()` status

## 📋 Pre-Production Checklist

Before going live:

- [ ] Switch to Stripe Live mode keys
- [ ] Update `STRIPE_KEY` and `STRIPE_SECRET` in production `.env`
- [ ] Create production webhook endpoint in Stripe Dashboard
- [ ] Update `STRIPE_WEBHOOK_SECRET` with production webhook secret
- [ ] Test complete subscription flow in production
- [ ] Set up monitoring for failed payments
- [ ] Configure email notifications (optional)
- [ ] Test subscription cancellation flow
- [ ] Test payment failure scenarios
- [ ] Verify trial period works correctly
- [ ] Test billing portal access

## 🎯 Quick Test Commands

```bash
# Verify setup
php verify-subscription-setup.php

# Run migrations
php artisan migrate

# Check routes
php artisan route:list | grep subscription

# Clear cache (if needed)
php artisan config:clear
php artisan cache:clear
```

## 📞 Need Help?

1. Check `STRIPE_SETUP.md` for detailed setup instructions
2. Review `SUBSCRIPTION_IMPLEMENTATION.md` for technical details
3. Check application logs: `storage/logs/laravel.log`
4. Check Stripe Dashboard for webhook events and errors
5. Review Laravel Cashier documentation: https://laravel.com/docs/cashier

