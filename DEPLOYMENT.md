# ArchiConnect App Deployment Guide

This guide provides instructions for deploying the ArchiConnect App to a cPanel shared hosting environment using Git Version Control.

## Prerequisites

- cPanel hosting account with PHP 8.2+ support
- MySQL database
- Git (for version control)
- Composer (for local development)
- Node.js and npm (for local development)

## Deployment Steps

### 1. Initial Setup (One-time)

1. Clone the repository to your local machine:

   ```bash
   git clone https://github.com/your-username/archiconnect-app.git
   cd archiconnect-app
   ```

2. Install dependencies:

   ```bash
   composer install
   npm install
   ```

3. Make sure your `.env.production` file is properly configured with your production database credentials and other settings.

4. Set up the Git repository in cPanel:
   - Log in to your cPanel account
   - Go to "Git Version Control"
   - Click "Create" to create a new repository
   - Name: `archiconnect-app`
   - Clone URL: Leave blank (will be filled automatically)
   - Repository Path: `public_html/login` (or your desired path)
   - Click "Create"

5. Add the cPanel repository as a remote to your local Git repository:

   ```bash
   git remote add cpanel https://architex@architex.co.za:2083/cpsess9411141945/frontend/jupiter/version_control/repositories/archiconnect-app.git
   ```

   (Replace the URL with the one provided by cPanel)

### 2. Deployment Process

1. Make your changes to the application code.

2. Build the frontend assets for production:

   ```bash
   chmod +x build-for-deployment.sh
   ./build-for-deployment.sh
   ```

3. Commit your changes:

   ```bash
   git add .
   git commit -m "Deployment update"
   ```

4. Push to the cPanel repository:

   ```bash
   git push cpanel main
   ```

5. The deployment will run automatically thanks to the `.cpanel.yml` file, which will:
   - Copy all necessary files to the deployment path
   - Set up the environment file
   - Create required directories
   - Set proper permissions
   - Install composer dependencies
   - Run Laravel commands (migrations, caching, etc.)

### 3. Verifying Deployment

After deployment, you can verify that everything is working correctly:

1. Visit your website: `https://login.architex.co.za`

2. If you need to check the PHP configuration, upload the `phpinfo.php` file and access it with your security token:

   ```text
   https://login.architex.co.za/phpinfo.php?token=your_secure_token
   ```

3. To check database connectivity, upload the `db-check.php` file and access it with your security token:

   ```text
   https://login.architex.co.za/db-check.php?token=your_secure_token
   ```

### 3. Configure cPanel PHP Settings

1. In cPanel, go to "Select PHP Version" or "PHP Configuration".

2. Make sure PHP 8.2 or higher is selected.

3. Enable the following PHP extensions:
   - BCMath
   - Ctype
   - Fileinfo
   - JSON
   - Mbstring
   - OpenSSL
   - PDO
   - Tokenizer
   - XML
   - GD (for image processing)

4. Set the following PHP values:
   - `memory_limit`: 256M or higher
   - `upload_max_filesize`: 64M or higher
   - `post_max_size`: 64M or higher
   - `max_execution_time`: 300 or higher

### 4. Set Up Cron Jobs

In cPanel, go to "Cron Jobs" and add the following cron job to run Laravel's scheduler:

```bash
* * * * * cd /home/architex/public_html/login && php artisan schedule:run >> /dev/null 2>&1
```

Replace `/home/architex/public_html/login` with the actual path to your application.

### 5. Configure Domain/Subdomain

1. In cPanel, go to "Domains" or "Subdomains".

2. Set up `login.architex.co.za` to point to the directory where you deployed the application.

3. Make sure SSL is enabled for your domain.

## Troubleshooting

### Common Issues

1. **500 Internal Server Error**:
   - Check the Laravel log file in `storage/logs/laravel.log`
   - Make sure storage and bootstrap/cache directories are writable
   - Verify that the .htaccess file is properly uploaded

2. **Database Connection Issues**:
   - Verify database credentials in the .env file
   - Check if the database exists and is accessible

3. **Missing Assets**:
   - Make sure you ran `npm run build` before deployment
   - Check if the assets were properly uploaded to the public directory

4. **Permission Issues**:
   - Set proper permissions for storage and bootstrap/cache directories:

     ```bash
     chmod -R 755 storage bootstrap/cache
     ```

## Maintenance and Updates

To update the application:

1. Make your changes locally
2. Run the deployment preparation script again
3. Upload and extract the new zip file
4. Run the post-deployment script

For database changes, make sure to run migrations after deployment:

```bash
php artisan migrate --force
```

## Security Considerations

1. Remove the `post-deployment.php` file after deployment
2. Keep your `.env` file secure and never commit it to version control
3. Regularly update dependencies to patch security vulnerabilities
4. Set up proper backups for your application and database

