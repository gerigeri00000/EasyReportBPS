<?php
/**
 * Temporary script to create storage symlink on shared hosting.
 *
 * INSTRUCTIONS:
 * 1. Upload this file to your Laravel project root (same level as artisan)
 * 2. Visit it in your browser: https://yourdomain.com/storage_link.php
 * 3. After it runs, DELETE THIS FILE immediately for security.
 *
 * This script creates the public/storage symlink pointing to storage/app/public
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";
echo "Creating storage symlink...\n";
$kernel->call('storage:link');
echo "Storage link created successfully!\n";
echo "Please DELETE this file immediately after use.\n";
echo "</pre>";