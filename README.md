# Office Report System - Laravel 11

A web application for BPS office to standardize partner (mitra) reports. Staff can create activities, partners can fill data via public links, and staff can generate non-editable PDF reports.

## Features

- **Staff Authentication**: Login system for office employees (registration disabled, accounts created manually)
- **Activity Management**: CRUD operations for activities with unique public URLs (UUID-based)
- **Partner Submission**: Public form for partners to submit respondent name and photo (no login required)
- **PDF Generation**: Automatic PDF report with A4 layout, activity header, and respondent photos using barryvdh/laravel-dompdf
- **Tailwind CSS**: Clean, responsive UI

## Requirements

- PHP 8.2+
- MySQL / MariaDB (or SQLite)
- Composer
- Node.js & npm
- GD library (for image uploads)
- DOMPDF dependencies (included via composer)

## Installation

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Environment Configuration

Update `.env` file:

```env
APP_NAME="Office Report System"
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=automate_report
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Generate application key:

```bash
php artisan key:generate
```

### 3. Database Setup

Create the database and run migrations:

```bash
php artisan migrate
```

### 4. Create Admin User

Create a seeder for admin staff:

```bash
php artisan make:seeder AdminUserSeeder
```

Edit `database/seeders/AdminUserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin Staff',
            'email' => 'admin@bps.go.id',
            'password' => Hash::make('YourPassword123'),
            'email_verified_at' => now(),
        ]);
    }
}
```

Then run:

```bash
php artisan db:seed --class=AdminUserSeeder
```

Alternatively, create user via Tinker:

```bash
php artisan tinker
>>> User::factory()->create(['email' => 'admin@bps.go.id', 'password' => Hash::make('password')]);
```

### 5. Storage Link (Important for Photo Uploads)

Photos are stored in `storage/app/public/reports` and served via `public/storage` symlink.

#### With SSH access:

```bash
php artisan storage:link
```

#### Without SSH (Shared Hosting)

Create a temporary file `storage_link.php` in project root:

```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('storage:link');
echo "Storage link created!";
```

Upload to server, visit it in browser, then delete immediately.

### 6. Build Frontend Assets

```bash
npm run build
```

### 7. Set Permissions

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Shared Hosting - Public Folder Setup

Most shared hosts use `public_html` as document root.

**Option 1: Point document root to `public/`** (preferred)
- In cPanel/Plesk, set document root to `/home/username/automate_report/public`

**Option 2: Move files to `public_html/`**
- Copy everything from `public/` to `public_html/`
- Edit `public_html/index.php` and adjust the paths:

```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

**Important**: Keep the rest of Laravel (app, bootstrap, vendor, etc.) outside `public_html` for security.

## PDF Configuration

Uses `barryvdh/laravel-dompdf` (v3.1+). Config at `config/dompdf.php`:

- Paper: A4 Portrait
- Remote assets: enabled (loads images)
- Public path: set automatically

Ensure GD library is enabled in PHP.

## Usage

### Staff Workflow

1. Login at `/login`
2. Dashboard shows stats
3. Create Activity: fill title, location, date, description
4. Copy the public submission URL from edit page
5. Share URL with partners
6. View submitted reports on activity detail page
7. Generate/download PDF reports

### Partner Submission (Public)

1. Visit `https://yourdomain.com/submit/{uuid}`
2. Enter name and upload photo (max 2MB, JPG/PNG)
3. Submit
4. See confirmation page

## Routes Overview

- `GET /` → redirects to login
- `GET|POST /login` → login
- `POST /logout` → logout (auth)
- `GET /dashboard` → dashboard (auth)
- `GET|POST /activities` → activities management (resource, auth)
- `GET /activities/{activity}/pdf/generate` → download PDF (auth)
- `GET /activities/{activity}/pdf/view` → view PDF in browser (auth)
- `GET /submit/{uuid}` → public form
- `POST /submit/{uuid}` → submit report

## Database Schema

**activities**
- id
- title (string)
- location (string)
- activity_date (date)
- description (text, nullable)
- uuid (char, unique, auto-generated)
- created_at, updated_at

**reports**
- id
- activity_id (foreign, on delete cascade)
- respondent_name (string)
- photo_path (string)
- created_at, updated_at

## Security Notes

- Registration is disabled (staff accounts must be manually created)
- Public submission URLs contain unpredictable UUIDs
- Photo uploads validated: max 2MB, jpg/png only
- PDF routes require authentication
- All activity management requires authentication

## Troubleshooting

### Storage link not working
- Verify `storage/app/public` exists and is writable
- On Windows shared hosting, symlink may require elevated privileges. Use the temporary PHP script method.

### PDF images not loading
- Confirm `config/dompdf.php` has `'enable_remote' => true` and `'public_path' => public_path()`
- Check that uploaded photos exist at `storage/app/public/reports/`
- Ensure file permissions allow reading the storage directory

### Sessions not persisting
- Ensure `SESSION_DRIVER=database` and run `php artisan session:table && php artisan migrate`
- Or set `SESSION_DRIVER=file`

## Packages Used

- laravel/breeze (authentication scaffolding)
- barryvdh/laravel-dompdf (PDF generation)
- spatie/laravel-permission (installed, optional for future role management)
- ramsey/uuid (used by Str::uuid())

## License

MIT. Laravel framework license applies.
# EasyReportBPS
# EasyReportBPS
