@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="max-w-md mx-auto mt-16">
    <div class="bg-gray-800 shadow-xl rounded-lg p-8 border border-gray-700">
        <h2 class="text-3xl font-bold mb-6 text-white tracking-tight">Register</h2>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-5">
                <label for="name" class="block text-sm font-semibold text-white mb-2">Name</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus
                       class="w-full px-4 py-3 border border-gray-700 bg-gray-900 text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 font-medium placeholder-gray-500">
                @error('name')
                    <p class="mt-2 text-sm text-red-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="email" class="block text-sm font-semibold text-white mb-2">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required
                       class="w-full px-4 py-3 border border-gray-700 bg-gray-900 text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 font-medium placeholder-gray-500">
                @error('email')
                    <p class="mt-2 text-sm text-red-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="password" class="block text-sm font-semibold text-white mb-2">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       class="w-full px-4 py-3 border border-gray-700 bg-gray-900 text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 font-medium placeholder-gray-500">
                @error('password')
                    <p class="mt-2 text-sm text-red-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-semibold text-white mb-2">Confirm Password</label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required
                       class="w-full px-4 py-3 border border-gray-700 bg-gray-900 text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 font-medium placeholder-gray-500">
            </div>

            <div>
                <button type="submit" class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors font-semibold shadow-lg">
                    Register
                </button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <p class="text-sm text-gray-400 font-medium">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-green-400 hover:text-green-300 font-semibold transition-colors">Login</a>
            </p>
        </div>
    </div>
</div>
@endsection

