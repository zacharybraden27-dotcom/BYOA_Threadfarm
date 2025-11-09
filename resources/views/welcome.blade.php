<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('threadfarm.app.name', 'Threadfarm') }} - Transform Blog Posts into Twitter Threads</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Rubik:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
        }
        .font-rubik {
            font-family: 'Rubik', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-900 min-h-screen text-white">
    <!-- Navigation -->
    <nav class="bg-gray-800 shadow-lg border-b border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-green-500 hover:text-green-400 transition-colors tracking-tight font-rubik">
                        {{ config('threadfarm.app.name', 'Threadfarm') }}
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('posts.index') }}" class="text-gray-400 hover:text-white transition-colors font-medium">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-white transition-colors font-medium">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition-colors font-medium">Login</a>
                        <a href="{{ route('register') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-500 transition-colors font-semibold shadow-lg">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-gray-900 to-gray-800 py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-5xl lg:text-7xl font-bold text-white mb-6 tracking-tight font-rubik">
                    Turn Your Blog Posts Into
                    <span class="text-green-500">Engaging Twitter Threads</span>
                </h1>
                <p class="text-xl lg:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto leading-relaxed">
                    Powered by Google's Gemini AI. Transform your long-form content into bite-sized, shareable Twitter threads in seconds.
                </p>
                @guest
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('register') }}" class="bg-green-600 text-white px-8 py-4 rounded-md hover:bg-green-500 transition-colors font-semibold shadow-lg text-lg">
                        Start Creating Threads
                    </a>
                    <a href="{{ route('login') }}" class="bg-gray-700 text-white px-8 py-4 rounded-md hover:bg-gray-600 transition-colors font-semibold text-lg">
                        Sign In
                    </a>
                </div>
                @else
                <div class="flex justify-center">
                    <a href="{{ route('posts.index') }}" class="bg-green-600 text-white px-8 py-4 rounded-md hover:bg-green-500 transition-colors font-semibold shadow-lg text-lg">
                        Go to Dashboard
                    </a>
                </div>
                @endguest
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold text-white mb-4 font-rubik">
                    Everything You Need to Grow Your Twitter Presence
                </h2>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                    Powerful features designed to make content creation effortless
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gray-800 rounded-lg p-8 border border-gray-700 hover:border-green-500/50 transition-all hover:shadow-xl">
                    <div class="w-14 h-14 bg-green-500/20 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3 font-rubik">AI-Powered Generation</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Our advanced AI analyzes your blog posts and automatically generates up to 10 engaging Twitter threads, perfectly formatted and ready to use.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gray-800 rounded-lg p-8 border border-gray-700 hover:border-green-500/50 transition-all hover:shadow-xl">
                    <div class="w-14 h-14 bg-green-500/20 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3 font-rubik">Edit & Customize</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Fine-tune every tweet before posting. Edit content, track character counts, and ensure your threads are exactly how you want them.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gray-800 rounded-lg p-8 border border-gray-700 hover:border-green-500/50 transition-all hover:shadow-xl">
                    <div class="w-14 h-14 bg-green-500/20 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3 font-rubik">Organize & Manage</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Keep track of all your posts and tweets. Archive old content, manage drafts, and track which tweets you've posted.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gray-800 rounded-lg p-8 border border-gray-700 hover:border-green-500/50 transition-all hover:shadow-xl">
                    <div class="w-14 h-14 bg-green-500/20 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3 font-rubik">Quick Copy & Paste</h3>
                    <p class="text-gray-400 leading-relaxed">
                        One-click copy to clipboard makes it easy to share your threads on Twitter. No complicated integrations needed.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gray-800 rounded-lg p-8 border border-gray-700 hover:border-green-500/50 transition-all hover:shadow-xl">
                    <div class="w-14 h-14 bg-green-500/20 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3 font-rubik">Character Count</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Automatic character counting ensures every tweet stays within Twitter's 280-character limit. Never worry about truncation again.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gray-800 rounded-lg p-8 border border-gray-700 hover:border-green-500/50 transition-all hover:shadow-xl">
                    <div class="w-14 h-14 bg-green-500/20 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3 font-rubik">Smart Status Tracking</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Mark tweets as draft, posted, or discarded. Keep your workflow organized and never lose track of what you've shared.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold text-white mb-4 font-rubik">
                    How It Works
                </h2>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                    Three simple steps to transform your content
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold text-white">
                        1
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4 font-rubik">Create Your Blog Post</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Write or paste your blog post content into Threadfarm. Add a title and save it to your dashboard.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold text-white">
                        2
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4 font-rubik">Generate Tweets</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Click "Generate Tweets" and let our AI create up to 10 engaging Twitter threads from your content.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold text-white">
                        3
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4 font-rubik">Edit & Share</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Review, edit, and customize your tweets. Then copy and paste them directly to Twitter.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    @guest
    <section class="py-20 bg-gradient-to-b from-gray-800 to-gray-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6 font-rubik">
                Ready to Transform Your Content?
            </h2>
            <p class="text-xl text-gray-400 mb-8 leading-relaxed">
                Join Threadfarm today and start turning your blog posts into engaging Twitter threads in minutes.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-green-600 text-white px-8 py-4 rounded-md hover:bg-green-500 transition-colors font-semibold shadow-lg text-lg">
                    Get Started for Free
                </a>
                <a href="{{ route('login') }}" class="bg-gray-700 text-white px-8 py-4 rounded-md hover:bg-gray-600 transition-colors font-semibold text-lg">
                    Sign In
                </a>
            </div>
        </div>
    </section>
    @endguest

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-gray-800 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-400">
                <p class="mb-2">© {{ date('Y') }} {{ config('threadfarm.app.name', 'Threadfarm') }}. All rights reserved.</p>
                <p class="text-sm">Powered by Google Gemini AI</p>
            </div>
        </div>
    </footer>
</body>
</html>
