# Quick Start Guide

## Important: Run commands from the `threadfarm` directory!

All commands must be run from the `threadfarm` directory, not the parent directory.

## Setup (First Time Only)

1. Navigate to the threadfarm directory:
```bash
cd threadfarm
```

2. Install dependencies (if not already installed):
```bash
composer install
npm install
```

3. Set up environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Add your Gemini API key to `.env`:
```
GEMINI_API_KEY=your-api-key-here
```

5. Run migrations:
```bash
php artisan migrate
```

## Starting the Application

You need to run **two commands in separate terminals**:

### Terminal 1 - Laravel Server
```bash
cd threadfarm
php artisan serve
```

### Terminal 2 - Vite Dev Server (for hot reloading)
```bash
cd threadfarm
npm run dev
```

**Important:** Make sure you're in the `threadfarm` directory for both commands!

## Access the Application

Once both servers are running, visit:
- **Application**: http://localhost:8000
- **Vite Dev Server**: http://localhost:5173 (runs automatically)

## Troubleshooting

### "Could not read package.json" Error
- Make sure you're in the `threadfarm` directory
- Run `pwd` to check your current directory
- You should see: `/Users/.../03. Threadfarm /threadfarm`

### "APP_KEY not set" Error
- Run: `php artisan key:generate`

### Database Errors
- Make sure `database/database.sqlite` exists
- Run: `php artisan migrate`

### Vite Not Working
- Make sure you're in the `threadfarm` directory
- Run: `npm install` to ensure dependencies are installed
- Check that `node_modules` directory exists

## Production Build

For production, build the assets:
```bash
cd threadfarm
npm run build
```

Then you only need to run the Laravel server:
```bash
php artisan serve
```

