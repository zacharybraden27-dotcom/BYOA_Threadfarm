# Troubleshooting Guide

## Issue: "Could not read package.json" Error

### Problem
When running `npm run dev`, you see:
```
npm error enoent Could not read package.json: Error: ENOENT: no such file or directory
```

### Solution
You're running the command from the wrong directory. The `package.json` file is in the `threadfarm` directory, not the parent directory.

### Fix
1. Navigate to the correct directory:
```bash
cd threadfarm
```

2. Verify you're in the right place:
```bash
pwd
# Should show: /Users/.../03. Threadfarm /threadfarm
```

3. Check that package.json exists:
```bash
ls package.json
# Should show: package.json
```

4. Now run your commands:
```bash
npm run dev
```

## Quick Start Commands

### Check Your Setup
```bash
cd threadfarm
./check-setup.sh
```

### Start the Application

**Terminal 1:**
```bash
cd threadfarm
php artisan serve
```

**Terminal 2:**
```bash
cd threadfarm
npm run dev
```

## Common Issues

### 1. Wrong Directory
- **Symptom**: "Could not read package.json" error
- **Fix**: Make sure you're in the `threadfarm` directory

### 2. Dependencies Not Installed
- **Symptom**: Module not found errors
- **Fix**: 
```bash
cd threadfarm
npm install
composer install
```

### 3. APP_KEY Not Set
- **Symptom**: Encryption key errors
- **Fix**:
```bash
cd threadfarm
php artisan key:generate
```

### 4. Database Not Created
- **Symptom**: Database connection errors
- **Fix**:
```bash
cd threadfarm
touch database/database.sqlite
php artisan migrate
```

### 5. GEMINI_API_KEY Not Set
- **Symptom**: Tweet generation fails
- **Fix**: Add your API key to `.env` file:
```
GEMINI_API_KEY=your-api-key-here
```

## Verify Setup

Run the setup checker:
```bash
cd threadfarm
./check-setup.sh
```

This will verify:
- ✅ Correct directory
- ✅ Dependencies installed
- ✅ Environment configured
- ✅ Database exists
- ✅ API keys set

## Still Having Issues?

1. Make sure you're in the `threadfarm` directory (not the parent)
2. Run `./check-setup.sh` to verify everything
3. Check the error messages carefully
4. Ensure all dependencies are installed: `npm install` and `composer install`

