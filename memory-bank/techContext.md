# Technical Context

## 1. Core Technologies

*   **Backend Framework:** PHP / Laravel (Version: To be determined - check `composer.json`)
    *   *Key Laravel Components Used:* Eloquent ORM, Blade Templating, Routing, Middleware, Artisan Console, Queues (if used), Events/Listeners, Service Container.
*   **Frontend:**
    *   HTML, CSS (Tailwind CSS - confirmed by `tailwind.config.js`)
    *   JavaScript (Specific libraries/frameworks TBD - check `package.json`, `resources/js`. Could be Alpine.js, Vue.js, React, or vanilla JS).
*   **Database:** (To be determined - check `config/database.php`, `.env` files. Common choices: MySQL, PostgreSQL, SQLite).
*   **Web Server:** (To be determined - e.g., Apache, Nginx. Often managed by hosting environment or local dev tools like Laragon, XAMPP, Docker).
*   **PHP Version:** (To be determined - check `composer.json` or `php -v` in the project environment).

## 2. Development Environment & Tools

*   **Package Managers:**
    *   `composer` (PHP dependencies - see `composer.json`, `composer.lock`)
    *   `npm` or `yarn` (JavaScript dependencies - see `package.json`, `package-lock.json` or `yarn.lock`)
*   **Build Tools:**
    *   Vite (`vite.config.js` is present) - For compiling frontend assets (JS, CSS).
*   **Version Control:** Git (`.gitignore` is present).
    *   *Repository URL:* (To be added if known)
*   **Local Development Setup:** (How developers run the project locally)
    *   Example: Laravel Sail, Laragon, XAMPP, MAMP, Docker, `php artisan serve`.
*   **IDE/Editor:** (User preference, e.g., VS Code - `architex-lava.code-workspace` suggests VS Code).
*   **Testing Framework:**
    *   PHPUnit (`phpunit.xml` is present) - For backend testing.
    *   Pest (`tests/Pest.php` is present) - Often used alongside or as an alternative to PHPUnit in Laravel.
    *   (Frontend testing tools TBD - e.g., Jest, Cypress).

## 3. Key Dependencies & Libraries

*   **(List major backend and frontend dependencies identified from `composer.json` and `package.json` once reviewed.)**
*   **Backend Examples (from `composer.json` - hypothetical until reviewed):**
    *   `laravel/framework`
    *   `laravel/sanctum` (API authentication)
    *   `spatie/laravel-permission` (Roles & Permissions)
    *   `barryvdh/laravel-debugbar` (Development tool)
*   **Frontend Examples (from `package.json` - hypothetical until reviewed):**
    *   `tailwindcss`
    *   `alpinejs`
    *   `axios` (HTTP client)
    *   `lodash` (Utility library)

## 4. APIs & Integrations

*   **(List any third-party APIs or services the application integrates with.)**
*   **Examples:**
    *   Payment Gateway (e.g., Stripe, PayPal)
    *   Email Service (e.g., Mailgun, SendGrid, AWS SES - check `config/mail.php`)
    *   Cloud Storage (e.g., AWS S3 - check `config/filesystems.php`)
    *   Real-time Notifications (e.g., Pusher, Ably - check `config/broadcasting.php`)
    *   Social Login (e.g., Google, Facebook - check `config/services.php`)

## 5. Deployment & Infrastructure

*   **Deployment Process:** (How is the application deployed to staging/production?)
    *   (Files like `deploy.sh`, `deploy-cpanel.sh`, `deploy.xml`, `DEPLOYMENT.md`, `post-deploy.sh` suggest a defined deployment process, possibly involving cPanel or custom scripts.)
    *   `build-for-deployment.sh` indicates a build step.
    *   `pro-deployer.js`, `pro-deployer-package.json`, `PRO-DEPLOYER-README.md` suggest use of a tool named "Pro Deployer".
*   **Hosting Environment:** (e.g., Shared Hosting, VPS, Managed Laravel Hosting, AWS, DigitalOcean).
    *   (cPanel related files suggest shared hosting or VPS with cPanel).
*   **CI/CD:** (Continuous Integration / Continuous Deployment pipeline, if any - e.g., GitHub Actions, GitLab CI).
*   **Environment Configuration:** `.env` files (`.env.example`, `.env.production`) for managing environment-specific settings.

## 6. Technical Constraints & Considerations

*   **Scalability:** (Any known limitations or plans for scaling?)
*   **Security:** (Key security measures - Laravel provides many out-of-the-box: CSRF protection, XSS filtering, SQL injection prevention via Eloquent. `.htaccess` file also present).
*   **Performance:** (Any performance targets or optimization strategies?)
*   **PHP Extensions:** (`check_extensions.php` suggests specific PHP extensions are required).

*This document should be updated as more technical details are discovered or decisions are made. It builds upon `projectbrief.md` and `systemPatterns.md`.*
