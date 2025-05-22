# ArchiConnect App cPanel Deployment Guide

This guide explains how to deploy the ArchiConnect App to a cPanel shared hosting environment.

## Prerequisites

- cPanel access with PHP 8.2+ support
- File Manager or FTP access
- Database access (MySQL)

## Preparing for Deployment

1. Run the deployment script locally:
   ```bash
   bash deploy-cpanel.sh
   ```

   This will:
   - Build Vite assets for production
   - Create a deployment directory with all required files
   - Attempt to create a zip file for easy uploading

2. Verify that all assets were built correctly by checking `public/build/manifest.json`

## Uploading to cPanel

1. Log in to your cPanel account
2. Navigate to the File Manager
3. Go to your desired installation directory (e.g., `public_html/login`)
4. Upload the `archiconnect-app-deployment.zip` file (or manually upload the entire `deployment` folder)
5. Extract the zip file in place

## Configuration

1. Make sure the `.env` file is properly configured with:
   - Database credentials
   - APP_ENV=production
   - APP_DEBUG=false
   - APP_URL set to your website URL

2. Ensure file permissions are set correctly:
   - `storage` and `bootstrap/cache` directories should be writable (755)
   - All other directories can be 755
   - All files should be 644

## Post-Deployment Steps

Run these commands either via Terminal (if available) or using the `post-deployment.php` script:

1. Generate application key (if not already set):
   ```bash
   php artisan key:generate
   ```

2. Run database migrations:
   ```bash
   php artisan migrate --force
   ```

3. Cache configurations for better performance:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. Create storage symlink:
   ```bash
   php artisan storage:link
   ```

## Troubleshooting

If you encounter issues with Vite assets:

1. Visit `/check-vite-assets.php` to verify that all assets are properly deployed
2. Check that your CSP headers in `.htaccess` allow loading from your own domain
3. Ensure that the `public/build` directory and all its contents were properly uploaded

For other issues, check the Laravel logs in `storage/logs/laravel.log`
