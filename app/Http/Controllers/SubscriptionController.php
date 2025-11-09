<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class SubscriptionController extends Controller
{
    /**
     * Show the pricing page
     */
    public function index()
    {
        $user = Auth::user();
        $subscription = $user->subscription('default');
        $onTrial = $user->onTrial('default');
        $subscribed = $user->subscribed('default');
        
        return view('subscriptions.index', [
            'subscription' => $subscription,
            'onTrial' => $onTrial,
            'subscribed' => $subscribed,
        ]);
    }

    /**
     * Handle checkout session creation
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        $priceId = config('services.stripe.price_id');

        if (!$priceId) {
            return back()->with('error', 'Subscription price not configured. Please contact support.');
        }

        try {
            $checkout = $user->newSubscription('default', $priceId)
                ->trialDays(7)
                ->checkout([
                    'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('subscription.index'),
                ]);

            return redirect($checkout->url);
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to create checkout session: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful checkout
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        $user = Auth::user();
        
        if (!$sessionId) {
            return redirect()->route('subscription.index')->with('error', 'Invalid checkout session.');
        }

        try {
            // Set Stripe API key
            Stripe::setApiKey(config('services.stripe.secret'));
            
            // Retrieve the checkout session from Stripe
            $session = Session::retrieve($sessionId, [
                'expand' => ['subscription', 'customer']
            ]);
            
            // Update user's Stripe ID if not set
            if ($session->customer && !$user->stripe_id) {
                $user->stripe_id = is_string($session->customer) ? $session->customer : $session->customer->id;
                $user->save();
            }
            
            // Get the subscription ID from the session
            $subscriptionId = null;
            if ($session->subscription) {
                $subscriptionId = is_string($session->subscription) ? $session->subscription : $session->subscription->id;
            }
            
            // If no subscription in session, try to get it from the customer
            if (!$subscriptionId && $session->customer) {
                $customerId = is_string($session->customer) ? $session->customer : $session->customer->id;
                $subscriptions = \Stripe\Subscription::all([
                    'customer' => $customerId,
                    'status' => 'all',
                    'limit' => 1,
                ]);
                
                if (!empty($subscriptions->data)) {
                    $subscriptionId = $subscriptions->data[0]->id;
                }
            }
            
            // If we have a subscription ID, sync it from Stripe
            if ($subscriptionId) {
                // Retrieve the full subscription from Stripe
                $stripeSubscription = \Stripe\Subscription::retrieve($subscriptionId);
                
                // Update user's trial_ends_at if subscription is on trial
                if ($stripeSubscription->trial_end) {
                    $user->trial_ends_at = \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end);
                    $user->save();
                }
                
                // Sync the subscription using Cashier
                // This will update the local database with the subscription details
                $user->subscriptions()->updateOrCreate(
                    [
                        'stripe_id' => $stripeSubscription->id,
                        'type' => 'default',
                    ],
                    [
                        'stripe_status' => $stripeSubscription->status,
                        'stripe_price' => $stripeSubscription->items->data[0]->price->id ?? null,
                        'quantity' => $stripeSubscription->items->data[0]->quantity ?? 1,
                        'trial_ends_at' => $stripeSubscription->trial_end ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end) : null,
                        'ends_at' => $stripeSubscription->ended_at ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->ended_at) : null,
                    ]
                );
                
                // Refresh user model to get updated subscription
                $user->refresh();
            }
            
            // Verify subscription is active or on trial
            if ($user->subscribed('default') || $user->onTrial('default')) {
                return redirect()->route('posts.index')->with('success', 'Welcome! Your subscription is active with a 7-day free trial.');
            }
            
            // If subscription still isn't active, it might be pending webhook processing
            // Give it a moment and check one more time
            sleep(2);
            $user->refresh();
            
            if ($user->subscribed('default') || $user->onTrial('default')) {
                return redirect()->route('posts.index')->with('success', 'Welcome! Your subscription is active with a 7-day free trial.');
            }
            
            // Last resort: redirect with info message
            return redirect()->route('subscription.index')->with('info', 'Your payment was successful! Your subscription should be active shortly. Please refresh this page in a moment.');
            
        } catch (\Exception $e) {
            \Log::error('Subscription success error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'session_id' => $sessionId,
                'user_id' => $user->id,
            ]);
            
            // Even if there's an error, check if subscription exists (webhook might have processed it)
            $user->refresh();
            if ($user->subscribed('default') || $user->onTrial('default')) {
                return redirect()->route('posts.index')->with('success', 'Welcome! Your subscription is active.');
            }
            
            return redirect()->route('subscription.index')->with('error', 'There was an issue activating your subscription. Please contact support if this persists.');
        }
    }

    /**
     * Create billing portal session
     */
    public function billingPortal(Request $request)
    {
        $user = Auth::user();

        try {
            return $user->redirectToBillingPortal(route('posts.index'));
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to create billing portal session: ' . $e->getMessage());
        }
    }

}
