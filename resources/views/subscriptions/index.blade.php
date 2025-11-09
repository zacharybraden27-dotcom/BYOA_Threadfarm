@extends('layouts.app')

@section('title', 'Subscribe - ' . config('threadfarm.app.name'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-4xl lg:text-5xl font-bold text-white mb-4">Choose Your Plan</h1>
        <p class="text-xl text-gray-400">Get started with a 7-day free trial. No credit card required until the trial ends.</p>
    </div>

    @if(session('error'))
        <div class="bg-red-900/50 border-l-4 border-red-500 text-red-300 p-4 mb-6 rounded-r-md shadow-lg" role="alert">
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif

    @if($subscribed || $onTrial)
        <div class="bg-green-900/50 border-l-4 border-green-500 text-green-300 p-6 mb-8 rounded-r-md shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold mb-2">You're all set!</h3>
                    @if($onTrial)
                        <p class="text-green-200">
                            You're currently on a free trial. 
                            @if($subscription && $subscription->trial_ends_at)
                                Your trial ends on {{ $subscription->trial_ends_at->format('F j, Y') }}.
                            @endif
                        </p>
                    @else
                        <p class="text-green-200">You have an active subscription.</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('subscription.billing-portal') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-500 transition-colors font-semibold">
                        Manage Subscription
                    </button>
                </form>
            </div>
        </div>
    @endif

    <div class="bg-gray-800 rounded-lg border-2 border-green-500 p-8 shadow-xl">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-white mb-2">Premium Plan</h2>
            <div class="flex items-baseline justify-center gap-2 mb-4">
                <span class="text-5xl font-bold text-white">$10</span>
                <span class="text-gray-400 text-xl">/month</span>
            </div>
            <p class="text-gray-400 mb-6">All features included</p>
        </div>

        <ul class="space-y-4 mb-8">
            <li class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-gray-300">AI-powered tweet generation from blog posts</span>
            </li>
            <li class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-gray-300">Generate up to 10 tweets per blog post</span>
            </li>
            <li class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-gray-300">Edit and customize tweets before posting</span>
            </li>
            <li class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-gray-300">Track tweet status (draft, posted, discarded)</span>
            </li>
            <li class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-gray-300">Unlimited blog posts and tweets</span>
            </li>
            <li class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-gray-300">Archive and organize your content</span>
            </li>
            <li class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-gray-300">7-day free trial - cancel anytime</span>
            </li>
        </ul>

        @if(!$subscribed && !$onTrial)
            <form method="POST" action="{{ route('subscription.checkout') }}" class="text-center">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-8 py-4 rounded-md hover:bg-green-500 transition-colors font-semibold shadow-lg text-lg w-full sm:w-auto">
                    Start 7-Day Free Trial
                </button>
            </form>
            <p class="text-center text-gray-400 text-sm mt-4">
                No credit card required until the trial ends
            </p>
        @endif
    </div>

    <div class="mt-8 text-center text-gray-400 text-sm">
        <p>Secure payment processing by <a href="https://stripe.com" target="_blank" class="text-green-500 hover:text-green-400 underline">Stripe</a></p>
        <p class="mt-2">Cancel your subscription anytime from your account settings</p>
    </div>
</div>
@endsection

