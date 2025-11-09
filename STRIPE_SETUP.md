# Stripe Subscription Setup Guide

This guide will help you set up Stripe subscriptions for your Threadfarm application.

## Prerequisites

1. A Stripe account (sign up at https://stripe.com)
2. Access to your Stripe Dashboard

## Step 1: Get Your Stripe API Keys

1. Log in to your [Stripe Dashboard](https://dashboard.stripe.com)
2. Go to **Developers** > **API keys**
3. Copy your **Publishable key** and **Secret key**
   - For testing, use the **Test mode** keys
   - For production, use the **Live mode** keys (switch toggle in top right)

## Step 2: Create a Product and Price in Stripe

1. In your Stripe Dashboard, go to **Products**
2. Click **+ Add product**
3. Fill in the product details:
   - **Name**: Threadfarm Premium
   - **Description**: Monthly subscription for Threadfarm
4. Under **Pricing**, set:
   - **Pricing model**: Standard pricing
   - **Price**: $10.00 USD
   - **Billing period**: Monthly
   - **Recurring**: Yes
5. Click **Save product**
6. After creating the product, you'll see a **Price ID** (starts with `price_`)
   - **Copy this Price ID** - you'll need it for your `.env` file

## Step 3: Configure Your .env File

Add the following environment variables to your `.env` file:

```env
STRIPE_KEY=pk_test_... (your publishable key)
STRIPE_SECRET=sk_test_... (your secret key)
STRIPE_WEBHOOK_SECRET=whsec_... (we'll get this in the next step)
STRIPE_PRICE_ID=price_... (the price ID from Step 2)
```

**Important**: 
- For testing, use test mode keys (start with `pk_test_` and `sk_test_`)
- For production, use live mode keys (start with `pk_live_` and `sk_live_`)

## Step 4: Set Up Stripe Webhooks

Stripe webhooks allow your application to receive real-time updates about subscription events (payment succeeded, subscription cancelled, etc.).

### For Local Development (using Stripe CLI):

1. Install the [Stripe CLI](https://stripe.com/docs/stripe-cli)
2. Login to Stripe CLI:
   ```bash
   stripe login
   ```
3. Forward webhooks to your local server:
   ```bash
   stripe listen --forward-to localhost:8000/cashier/webhook
   ```
4. The CLI will display a webhook signing secret (starts with `whsec_`)
5. Add this to your `.env` file as `STRIPE_WEBHOOK_SECRET`

### For Production:

1. In your Stripe Dashboard, go to **Developers** > **Webhooks**
2. Click **+ Add endpoint**
3. Enter your endpoint URL: `https://yourdomain.com/cashier/webhook`
4. Select the following events to listen to:
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
   - `customer.updated`
   - `payment_method.attached`
5. Click **Add endpoint**
6. Copy the **Signing secret** (starts with `whsec_`)
7. Add this to your production `.env` file as `STRIPE_WEBHOOK_SECRET`

## Step 5: Run Database Migrations

Make sure your database is set up and run the migrations:

```bash
php artisan migrate
```

This will create the necessary tables for subscriptions:
- `subscriptions` - stores subscription information
- `subscription_items` - stores subscription item details
- Adds subscription columns to the `users` table

## Step 6: Test Your Setup

1. Start your application:
   ```bash
   php artisan serve
   ```

2. Register a new account or log in
3. You should be redirected to the subscription page
4. Click "Start 7-Day Free Trial"
5. You'll be redirected to Stripe Checkout
6. Use Stripe's test card numbers:
   - **Card number**: `4242 4242 4242 4242`
   - **Expiry**: Any future date (e.g., `12/34`)
   - **CVC**: Any 3 digits (e.g., `123`)
   - **ZIP**: Any 5 digits (e.g., `12345`)

7. Complete the checkout process
8. You should be redirected back to your application with an active subscription

## Troubleshooting

### Webhook Errors

If you're getting webhook errors:
- Make sure `STRIPE_WEBHOOK_SECRET` is set correctly in your `.env` file
- For local development, use Stripe CLI to forward webhooks
- Check your application logs for detailed error messages

### Subscription Not Activating

- Verify that `STRIPE_PRICE_ID` is correct in your `.env` file
- Check that the price ID matches the one in your Stripe Dashboard
- Ensure your Stripe keys are in the correct mode (test vs live)

### Migration Errors

- Make sure your database is properly configured
- Check that SQLite database file exists: `database/database.sqlite`
- If using a different database, update `DB_CONNECTION` in your `.env` file

## Production Checklist

Before going live:

- [ ] Switch to Stripe Live mode keys
- [ ] Update `STRIPE_KEY` and `STRIPE_SECRET` in production `.env`
- [ ] Create a production webhook endpoint in Stripe Dashboard
- [ ] Update `STRIPE_WEBHOOK_SECRET` with production webhook secret
- [ ] Test the complete subscription flow in production
- [ ] Set up monitoring for failed payments
- [ ] Configure email notifications for subscription events (optional)

## Additional Resources

- [Laravel Cashier Documentation](https://laravel.com/docs/cashier)
- [Stripe Documentation](https://stripe.com/docs)
- [Stripe Testing Guide](https://stripe.com/docs/testing)

