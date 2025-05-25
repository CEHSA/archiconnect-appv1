# Manual cPanel Deployment Guide

## Pre-Deployment Steps (Local)

1. Build frontend assets:
   ```bash
   npm install
   npm run build
   ```
   This will create the production assets in the `public/build` directory.

2. Verify that `public/build/manifest.json` exists after the build.

3. Create a deployment archive excluding development files:
   - Create a ZIP file containing:
     ```
     app/
     bootstrap/
     config/
     database/
     public/
     resources/
     routes/
     storage/
     vendor/
     .htaccess
     artisan
     composer.json
     composer.lock
     .env.production (rename to .env on server)
     ```
   - EXCLUDE:
     ```
     .git/
     node_modules/
     tests/
     .env
     .env.example
     phpunit.xml
     ```

## Deployment Steps (cPanel)

1. **Database Setup**:
   - Create a new MySQL database in cPanel
   - Create a database user and assign to the database
   - Update the database credentials in your `.env` file

2. **File Upload**:
   - Login to cPanel File Manager
   - Navigate to `public_html` (or your custom subdomain directory)
   - Upload the deployment ZIP file
   - Extract the ZIP file
   - Rename `.env.production` to `.env`

3. **File Permissions**:
   - Set directories to 755:
     ```
     storage/
     bootstrap/cache/
     public/build/
     ```
   - Set files to 644:
     ```
     .env
     public/.htaccess
     ```

4. **Environment Setup**:
   - Edit `.env` file:
     - Update `APP_URL`
     - Set `APP_ENV=production`
     - Set `APP_DEBUG=false`
     - Update database credentials
     - Update mail settings
     - Generate APP_KEY using cPanel Terminal:
       ```php
       php artisan key:generate --force
       ```

5. **Database Migration**:
   - Access the cPanel Terminal (if available) or use PHP MyAdmin
   - Run migrations:
     ```php
     php artisan migrate --force
     ```

6. **Storage Setup**:
   - Create storage link:
     ```php
     php artisan storage:link
     ```

7. **Cache Configuration**:
   ```php
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Post-Deployment Verification

1. **Environment Check**:
   - Access `https://your-domain.com/env-check.php` with header:
     ```
     X-Deployment-Check: secure_check_token
     ```
   - Verify all checks pass
   - Delete `env-check.php` after verification

2. **Manual Checks**:
   - Test the login system
   - Verify static assets are loading
   - Check file upload functionality
   - Test database operations
   - Verify email functionality

3. **Security Cleanup**:
   - Remove any development/debug files
   - Ensure proper file permissions
   - Verify `.env` is not publicly accessible

## Troubleshooting

1. **500 Server Error**:
   - Check PHP error logs in cPanel
   - Verify storage permissions
   - Ensure all required PHP extensions are enabled

2. **Assets Not Loading**:
   - Verify manifest.json exists
   - Check asset paths in views
   - Clear browser cache

3. **Database Connection Issues**:
   - Verify database credentials
   - Check database user permissions
   - Ensure proper character set configuration

## Maintenance

1. Regular Checks:
   - Monitor error logs
   - Check disk space usage
   - Review security settings

2. Updates:
   - Keep Laravel framework updated
   - Monitor security advisories
   - Update dependencies when needed

## Security Notes

1. Ensure these files/directories are not publicly accessible:
   - `.env`
   - `composer.json`
   - `package.json`
   - `artisan`
   - `storage/`
   - `database/`

2. Keep backups of:
   - Database
   - Storage files
   - Environment configuration
