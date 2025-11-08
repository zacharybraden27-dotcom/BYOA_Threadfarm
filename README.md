# Threadfarm

A web application that automatically generates Twitter/X tweets from blog posts using Google's Gemini Flash AI.

## Features

- **User Authentication**: Simple email/password login and registration
- **Blog Post Management**: Create, edit, archive, and delete blog posts
- **AI-Powered Tweet Generation**: Automatically generate 10 tweets from blog post content using Gemini Flash
- **Tweet Management**: 
  - Mark tweets as posted to avoid duplicates
  - Discard unwanted tweets
  - Edit tweets before posting
  - Copy tweets to clipboard for easy posting
  - Character count validation (280 characters)
- **Search**: Search through your blog posts
- **Mobile-Responsive**: Beautiful, modern UI that works on all devices
- **Dark Mode**: Supports both light and dark themes

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- Google Gemini API Key

## Installation

**⚠️ Important: All commands must be run from the `threadfarm` directory!**

1. Navigate to the threadfarm directory:
```bash
cd threadfarm
```

2. Verify you're in the correct directory (should show `threadfarm`):
```bash
pwd
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies:
```bash
npm install
```

4. Set up environment variables:
```bash
cp .env.example .env
php artisan key:generate
```

5. Edit `.env` file and add your Gemini API key:
```
GEMINI_API_KEY=your-api-key-here
```

6. Run database migrations:
```bash
php artisan migrate
```

7. Build frontend assets:
```bash
npm run build
```

## Running the Application

### Development Mode

**⚠️ Important: Make sure you're in the `threadfarm` directory!**

You need to run **two commands in separate terminals**:

**Terminal 1 - Laravel Server:**
```bash
cd threadfarm  # Make sure you're in this directory!
php artisan serve
```

**Terminal 2 - Vite Dev Server (for hot reloading):**
```bash
cd threadfarm  # Make sure you're in this directory!
npm run dev
```

Visit `http://localhost:8000` in your browser.

> **Note:** If you see "Could not read package.json" error, you're in the wrong directory. Make sure you're in the `threadfarm` folder, not the parent folder.

### Production Mode

Build the assets for production:
```bash
npm run build
```

## Getting a Gemini API Key

1. Go to [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Sign in with your Google account
3. Create a new API key
4. Copy the API key and add it to your `.env` file as `GEMINI_API_KEY`

## Usage

1. **Register/Login**: Create an account or login with your credentials
2. **Create a Blog Post**: Click "Create New Post" and paste your blog post content
3. **Generate Tweets**: Click "Generate Tweets" to create 10 tweets from your blog post
4. **Manage Tweets**: 
   - Edit tweets if needed
   - Copy tweets to clipboard
   - Mark tweets as posted when you've shared them
   - Discard tweets you don't want to use
5. **Search Posts**: Use the search bar to find specific posts
6. **Archive Posts**: Archive posts you're done with to keep your list clean

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: Tailwind CSS 4, Blade Templates
- **Database**: SQLite (default)
- **AI**: Google Gemini Flash API
- **JavaScript**: Vanilla JS (no framework dependencies)

## Project Structure

```
threadfarm/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/          # Authentication controllers
│   │   ├── BlogPostController.php
│   │   └── TweetController.php
│   ├── Models/
│   │   ├── BlogPost.php
│   │   ├── Tweet.php
│   │   └── User.php
│   └── Services/
│       └── GeminiService.php  # Gemini API integration
├── database/
│   └── migrations/        # Database migrations
├── resources/
│   └── views/
│       ├── auth/          # Login/Register views
│       ├── posts/         # Blog post views
│       └── layouts/       # Main layout
└── routes/
    └── web.php            # Application routes
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
