# Threadfarm Design Documentation

## Overview

Threadfarm is a Laravel 12 web application that automatically generates Twitter/X tweets from blog posts using Google's Gemini Flash AI. The application follows a traditional MVC architecture with service layer integration for external API calls.

## Architecture

### Technology Stack

- **Backend Framework**: Laravel 12
- **Database**: SQLite (default, can be configured for other databases)
- **Frontend**: Blade templates with Tailwind CSS 4
- **JavaScript**: Vanilla JS (no framework dependencies)
- **AI Service**: Google Gemini Flash API (via HTTP client)
- **Authentication**: Laravel's built-in authentication system

### Design Patterns

1. **MVC (Model-View-Controller)**: Traditional Laravel architecture
2. **Service Layer**: `GeminiService` handles external API integration
3. **Repository Pattern**: Eloquent models act as repositories
4. **Dependency Injection**: Services injected into controllers via Laravel's service container

## Database Schema

### Users Table
- `id` (primary key)
- `name` (string)
- `email` (string, unique)
- `password` (hashed)
- `email_verified_at` (timestamp, nullable)
- `remember_token` (string, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Relationships**:
- `hasMany` BlogPost

### Blog Posts Table
- `id` (primary key)
- `user_id` (foreign key → users.id, onDelete: cascade)
- `title` (string)
- `content` (text)
- `archived_at` (timestamp, nullable) - Soft delete for archiving
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Relationships**:
- `belongsTo` User
- `hasMany` Tweet

**Accessors**:
- `unused_tweets_count` - Count of draft tweets

### Tweets Table
- `id` (primary key)
- `blog_post_id` (foreign key → blog_posts.id, onDelete: cascade)
- `content` (text)
- `status` (enum: 'draft', 'posted', 'discarded', default: 'draft')
- `character_count` (integer, default: 0) - Auto-calculated on save
- `posted_at` (timestamp, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Relationships**:
- `belongsTo` BlogPost

**Model Events**:
- `saving` - Automatically calculates `character_count` from content

## Models

### User Model (`app/Models/User.php`)
- Extends `Illuminate\Foundation\Auth\User`
- Uses `HasFactory` and `Notifiable` traits
- **Relationships**: `hasMany(BlogPost::class)`

### BlogPost Model (`app/Models/BlogPost.php`)
- Extends `Illuminate\Database\Eloquent\Model`
- **Fillable**: `user_id`, `title`, `content`, `archived_at`
- **Casts**: `archived_at` → datetime
- **Relationships**:
  - `belongsTo(User::class)`
  - `hasMany(Tweet::class)`
- **Accessors**: `getUnusedTweetsCountAttribute()` - Returns count of draft tweets

### Tweet Model (`app/Models/Tweet.php`)
- Extends `Illuminate\Database\Eloquent\Model`
- **Fillable**: `blog_post_id`, `content`, `status`, `character_count`, `posted_at`
- **Casts**: `posted_at` → datetime
- **Relationships**: `belongsTo(BlogPost::class)`
- **Model Events**: `boot()` method calculates `character_count` on save

## Controllers

### BlogPostController (`app/Http/Controllers/BlogPostController.php`)

**Routes**:
- `GET /posts` - Index (with search and pagination)
- `GET /posts/create` - Show create form
- `POST /posts` - Store new post
- `GET /posts/{post}` - Show post with tweets
- `GET /posts/{post}/edit` - Show edit form
- `PUT /posts/{post}` - Update post
- `DELETE /posts/{post}` - Delete post
- `POST /posts/{post}/archive` - Archive post
- `POST /posts/{post}/unarchive` - Unarchive post
- `GET /posts/archived` - Show archived posts
- `POST /posts/{post}/generate-tweets` - Generate tweets using Gemini

**Key Methods**:
- `index()` - Lists active posts with search and unused tweets count
- `show()` - Displays post with tweets grouped by status
- `generateTweets()` - Calls GeminiService to generate tweets, replaces existing drafts
- `archive()` / `unarchive()` - Soft delete/restore using `archived_at` timestamp

**Authorization**: All methods check `$post->user_id === Auth::id()`

### TweetController (`app/Http/Controllers/TweetController.php`)

**Routes**:
- `PUT /tweets/{tweet}` - Update tweet content
- `POST /tweets/{tweet}/mark-posted` - Mark tweet as posted
- `POST /tweets/{tweet}/discard` - Discard tweet
- `POST /tweets/{tweet}/restore` - Restore discarded tweet to draft

**Key Methods**:
- `update()` - Updates tweet content (max 280 characters)
- `markAsPosted()` - Sets status to 'posted' and records `posted_at` timestamp
- `discard()` - Sets status to 'discarded'
- `restore()` - Sets status back to 'draft' and clears `posted_at`

**Authorization**: All methods check tweet's blog post belongs to authenticated user

### Auth Controllers
- `LoginController` - Handles user login
- `LogoutController` - Handles user logout
- `RegisterController` - Handles user registration

## Services

### GeminiService (`app/Services/GeminiService.php`)

**Purpose**: Handles integration with Google Gemini Flash API for tweet generation.

**Configuration** (from `config/services.php` and environment):
- `api_key` - Gemini API key (required)
- `model` - Model name (default: 'gemini-2.5-flash')
- `base_url` - API base URL (default: 'https://generativelanguage.googleapis.com')
- `api_version` - API version (default: 'v1')

**Generation Settings** (configurable via `config/threadfarm.php`):
- `temperature` - Creativity level (default: 0.7)
- `top_k` - Top K sampling (default: 40)
- `top_p` - Nucleus sampling (default: 0.95)
- `max_output_tokens` - Maximum tokens in response (default: 2048)
- `tweet_count` - Number of tweets to generate (default: 10)
- `max_character_count` - Maximum characters per tweet (default: 280)

**Key Methods**:
- `generateTweets(string $blogPostContent, string $blogPostTitle = '')` - Main method to generate tweets
- `buildPrompt()` - Constructs the prompt for Gemini API
- `parseTweets()` - Parses Gemini response into array of tweet strings

**Error Handling**:
- Validates API key presence
- Logs API errors
- Throws exceptions with descriptive messages
- Handles empty responses and parsing failures

**Response Processing**:
- Removes numbering, bullets, hashtags, and mentions
- Filters tweets by character limit
- Ensures exactly 10 tweets (pads or truncates as needed)

## Routes

### Public Routes
- `GET /` - Redirects to posts index
- `GET /login` - Show login form (guest only)
- `POST /login` - Process login (guest only)
- `GET /register` - Show registration form (guest only)
- `POST /register` - Process registration (guest only)

### Authenticated Routes
- `POST /logout` - Logout user
- `GET /posts` - List posts
- `GET /posts/create` - Create post form
- `POST /posts` - Store post
- `GET /posts/{post}` - Show post
- `GET /posts/{post}/edit` - Edit post form
- `PUT /posts/{post}` - Update post
- `DELETE /posts/{post}` - Delete post
- `POST /posts/{post}/generate-tweets` - Generate tweets
- `POST /posts/{post}/archive` - Archive post
- `POST /posts/{post}/unarchive` - Unarchive post
- `GET /posts/archived` - List archived posts
- `PUT /tweets/{tweet}` - Update tweet
- `POST /tweets/{tweet}/mark-posted` - Mark as posted
- `POST /tweets/{tweet}/discard` - Discard tweet
- `POST /tweets/{tweet}/restore` - Restore tweet

## Configuration

### Application Configuration (`config/app.php`)
- Application name: `APP_NAME` (default: 'Laravel')
- Environment: `APP_ENV` (default: 'production')
- Debug mode: `APP_DEBUG` (default: false)
- Timezone: UTC
- Locale: `APP_LOCALE` (default: 'en')

### Services Configuration (`config/services.php`)
- Gemini API key: `GEMINI_API_KEY` (required)

### Threadfarm Configuration (`config/threadfarm.php`)
Centralized configuration for application-specific settings:

**Tweet Settings**:
- `tweet.max_character_count` - Maximum characters per tweet (default: 280)
- `tweet.count` - Number of tweets to generate (default: 10)
- `tweet.statuses` - Available tweet statuses: ['draft', 'posted', 'discarded']

**Pagination Settings**:
- `pagination.posts_per_page` - Posts per page (default: 12)

**Gemini API Settings**:
- `gemini.temperature` - Generation temperature (default: 0.7)
- `gemini.top_k` - Top K sampling (default: 40)
- `gemini.top_p` - Nucleus sampling (default: 0.95)
- `gemini.max_output_tokens` - Max tokens in response (default: 2048)

**UI Settings**:
- `app.name` - Application display name (default: 'Threadfarm')
- `app.title` - Page title suffix (default: 'Threadfarm - Blog Post to Tweets')

## Views

### Layout
- `resources/views/layouts/app.blade.php` - Main application layout
  - Dark mode by default
  - Navigation bar with app name and user info
  - Success/error message display
  - CSRF token included

### Authentication Views
- `resources/views/auth/login.blade.php` - Login form
- `resources/views/auth/register.blade.php` - Registration form

### Blog Post Views
- `resources/views/posts/index.blade.php` - Posts list with search
- `resources/views/posts/create.blade.php` - Create post form
- `resources/views/posts/edit.blade.php` - Edit post form
- `resources/views/posts/show.blade.php` - Post detail with tweets
- `resources/views/posts/archived.blade.php` - Archived posts list

## UI/UX Design

### Design System
- **Color Scheme**: Dark mode (gray-900 background, gray-800 nav)
- **Accent Color**: Green (green-500, green-600)
- **Typography**: Space Grotesk and Rubik fonts from Google Fonts
- **Responsive**: Mobile-first design with Tailwind CSS breakpoints

### Key UI Features
- Color-coded tweet statuses (draft, posted, discarded)
- Character count display for tweets
- Copy to clipboard functionality
- Search functionality with clear button
- Archive/delete with confirmation dialogs
- Success/error flash messages
- Responsive grid layouts

## Security

### Authentication
- Laravel's built-in authentication system
- Password hashing via bcrypt
- Session-based authentication
- CSRF protection on all forms

### Authorization
- All post operations check `user_id === Auth::id()`
- All tweet operations check tweet's blog post belongs to user
- Route middleware: `auth` for protected routes, `guest` for login/register

### Data Validation
- Blog post: title (required, max 255), content (required)
- Tweet: content (required, max 280 characters)
- Email validation on registration
- Password requirements (handled by Laravel)

## Error Handling

### Service Layer
- `GeminiService` throws exceptions with descriptive messages
- API errors are logged via Laravel's Log facade
- Graceful fallback for missing API key

### User-Facing Errors
- Flash messages for success/error states
- 403 errors for unauthorized access
- Validation errors displayed in forms
- Try-catch blocks in controllers return user-friendly error messages

## Data Flow

### Tweet Generation Flow
1. User clicks "Generate Tweets" on post detail page
2. `BlogPostController::generateTweets()` is called
3. `GeminiService::generateTweets()` is invoked with post content and title
4. Service builds prompt and calls Gemini API
5. Response is parsed into array of tweets
6. Existing draft tweets are deleted
7. New tweets are created with status 'draft'
8. User is redirected to post detail page with success message

### Tweet Status Flow
- **Draft** (default) → User can edit, mark as posted, or discard
- **Posted** → Tweet marked as posted with timestamp, cannot be edited
- **Discarded** → Tweet hidden from main view, can be restored to draft

## Environment Variables

### Required
- `APP_KEY` - Laravel encryption key
- `GEMINI_API_KEY` - Google Gemini API key (required for tweet generation)

### Optional
- `APP_NAME` - Application name (default: 'Laravel')
- `APP_ENV` - Environment (default: 'production')
- `APP_DEBUG` - Debug mode (default: false)
- `APP_URL` - Application URL (default: 'http://localhost')
- `APP_LOCALE` - Application locale (default: 'en')
- `GEMINI_MODEL` - Gemini model name (default: 'gemini-2.5-flash')
- `DB_CONNECTION` - Database connection (default: 'sqlite')
- `DB_DATABASE` - Database path for SQLite (default: 'database/database.sqlite')

## Constants and Reusable Values

All application-specific constants are centralized in `config/threadfarm.php`:

- Tweet character limit: `config('threadfarm.tweet.max_character_count')` (default: 280)
- Number of tweets: `config('threadfarm.tweet.count')` (default: 10)
- Tweet statuses: `config('threadfarm.tweet.statuses')` (default: ['draft', 'posted', 'discarded'])
- Posts per page: `config('threadfarm.pagination.posts_per_page')` (default: 12)
- Gemini settings: `config('threadfarm.gemini.*')`
- App name: `config('threadfarm.app.name')` (default: 'Threadfarm')
- App title: `config('threadfarm.app.title')` (default: 'Threadfarm - Blog Post to Tweets')

### Important Note on Database Schema

The database migration for the `tweets` table uses an enum column with hardcoded values: `['draft', 'posted', 'discarded']`. These values **must match** the values in `config('threadfarm.tweet.statuses')`. If you need to change the status values, you must:

1. Update the config file
2. Create a migration to alter the enum column in the database
3. Update all code references to use the new status values

Currently, the migration and config are in sync, so no changes are needed.

## Future Considerations

### Potential Improvements
1. Queue system for tweet generation (async processing)
2. Twitter/X API integration for direct posting
3. Tweet scheduling functionality
4. Analytics and statistics
5. Multi-user collaboration
6. Export functionality (CSV, JSON)
7. Batch operations (bulk archive, delete)
8. Advanced search filters
9. Tweet templates
10. AI model selection (different models for different use cases)

### Scalability
- Current design uses SQLite (suitable for single-user or small deployments)
- Can easily migrate to PostgreSQL/MySQL for multi-user scenarios
- Service layer allows easy replacement of AI provider
- Stateless architecture (except sessions) allows horizontal scaling

