<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        if ($sessionId) {
            return redirect()->route('posts.index')->with('success', 'Welcome! Your subscription is active with a 7-day free trial.');
        }

        return redirect()->route('subscription.index');
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
