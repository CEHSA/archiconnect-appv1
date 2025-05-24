# Technical Context

## 1. Core Technologies

* **Backend Framework:** PHP / Laravel (Version: To be determined - check `composer.json`)
  * *Key Laravel Components Used:* Eloquent ORM, Blade Templating, Routing, Middleware, Artisan Console, Queues (if used), Events/Listeners, Service Container.
* **Frontend:**
  * HTML, CSS (Tailwind CSS - confirmed by `tailwind.config.js`).
    * *Standard Light Theme (Adopted May 2025) - Key Utility Patterns (Site-wide):*
      * Main Content Card: `bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300`
      * Primary Buttons & Table Headers: `bg-cyan-700 text-white` (combined with other relevant button/text styling utilities).
      * Secondary/Back Buttons: `bg-gray-200 hover:bg-gray-300 text-gray-700` (plus font/padding utilities).
      * Edit/Contextual Buttons: `bg-yellow-500 text-gray-800` (plus font/padding utilities).
      * Label-over-value (Detail Views): Labels (`<dt>`) typically `block text-sm font-medium text-gray-500`; Values (`<dd>`) `mt-1 text-sm text-gray-900`.
      * Active Tab Indicator: `border-teal-500 text-teal-600`.
      * (Refer to `systemPatterns.md` for a more comprehensive list of UI/UX styling conventions for this theme).
  * JavaScript (Specific libraries/frameworks TBD - check `package.json`, `resources/js`. Could be Alpine.js, Vue.js, React, or vanilla JS).
* **Database:** MySQL (Confirmed via user input from `.env` file: `DB_CONNECTION=mysql`).
  * Host: `169.239.218.60`
  * Port: `3306`
  * Database Name: `architex_backend78`
  * User: `architex_main`
* **Web Server:** (To be determined - e.g., Apache, Nginx. Often managed by hosting environment or local dev tools like Laragon, XAMPP, Docker).
* **PHP Version:** (To be determined - check `composer.json` or `php -v` in the project environment).

## 2. Development Environment & Tools

* **Package Managers:**
  * `composer` (PHP dependencies - see `composer.json`, `composer.lock`)
  * `npm` or `yarn` (JavaScript dependencies - see `package.json`, `package-lock.json` or `yarn.lock`)
* **Build Tools:**
  * Vite (`vite.config.js` is present) - For compiling frontend assets (JS, CSS).
* **Version Control:** Git (`.gitignore` is present).
  * *Repository URL:* (To be added if known)
* **Local Development Setup:** (How developers run the project locally)
  * Example: Laravel Sail, Laragon, XAMPP, MAMP, Docker, `php artisan serve`.
* **IDE/Editor:** (User preference, e.g., VS Code - `architex-lava.code-workspace` suggests VS Code).
* **Testing Framework:**
  * PHPUnit (`phpunit.xml` is present) - For backend testing.
  * Pest (`tests/Pest.php` is present) - Often used alongside or as an alternative to PHPUnit in Laravel.
  * (Frontend testing tools TBD - e.g., Jest, Cypress).
  * **Testing Strategy (Adopted 5/24/2025):** An iterative phased implementation approach is followed. Comprehensive PHPUnit tests are executed after the completion of each development phase to ensure stability and quality before proceeding.

## 3. Key Dependencies & Libraries

* **(List major backend and frontend dependencies identified from `composer.json` and `package.json` once reviewed.)**
* **Backend Examples (from `composer.json` - hypothetical until reviewed):**
  * `laravel/framework`
  * `laravel/sanctum` (API authentication)
  * `spatie/laravel-permission` (Roles & Permissions)
  * `barryvdh/laravel-debugbar` (Development tool)
* **Frontend Examples (from `package.json` - hypothetical until reviewed):**
  * `tailwindcss`
  * `alpinejs`
  * `axios` (HTTP client)
  * `lodash` (Utility library)

## 4. APIs & Integrations

* **(List any third-party APIs or services the application integrates with.)**
* **Model Context Protocol (MCP) Servers:**
  * **`mysql-query-server` (Custom Local Server):**
    * **Purpose:** Provides direct MySQL query execution capability.
    * **Location:** `C:\Users\Raidmax i5\Documents\Cline\MCP\mysql-query-server`
    * **Tool(s):** `execute_mysql_query` (takes a SQL query string as input).
    * **Configuration:** Managed in `c:\Users\Raidmax i5\AppData\Roaming\Code - Insiders\User\globalStorage\saoudrizwan.claude-dev\settings\cline_mcp_settings.json`.
    * **Environment Variables Used by Server:** `MYSQL_HOST`, `MYSQL_PORT`, `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_DATABASE`.
    * **Dependencies:** `mysql2` (Node.js package).
* **Examples (Other Potential Integrations):**
  * Payment Gateway (e.g., Stripe, PayPal)
  * Email Service (e.g., Mailgun, SendGrid, AWS SES - check `config/mail.php`)
  * Cloud Storage (e.g., AWS S3 - check `config/filesystems.php`)
  * Real-time Notifications (e.g., Pusher, Ably - check `config/broadcasting.php`)
  * Social Login (e.g., Google, Facebook - check `config/services.php`)

## 5. Deployment & Infrastructure

* **Deployment Process:** (How is the application deployed to staging/production?)
  * (Files like `deploy.sh`, `deploy-cpanel.sh`, `deploy.xml`, `DEPLOYMENT.md`, `post-deploy.sh` suggest a defined deployment process, possibly involving cPanel or custom scripts.)
  * `build-for-deployment.sh` indicates a build step.
  * `pro-deployer.js`, `pro-deployer-package.json`, `PRO-DEPLOYER-README.md` suggest use of a tool named "Pro Deployer".
* **Hosting Environment:** (e.g., Shared Hosting, VPS, Managed Laravel Hosting, AWS, DigitalOcean).
  * (cPanel related files suggest shared hosting or VPS with cPanel).
* **CI/CD:** (Continuous Integration / Continuous Deployment pipeline, if any - e.g., GitHub Actions, GitLab CI).
* **Environment Configuration:** `.env` files (`.env.example`, `.env.production`) for managing environment-specific settings.

## 6. Technical Constraints & Considerations

* **Scalability:** (Any known limitations or plans for scaling?)
* **Security:** (Key security measures - Laravel provides many out-of-the-box: CSRF protection, XSS filtering, SQL injection prevention via Eloquent. `.htaccess` file also present).
* **Performance:** (Any performance targets or optimization strategies?)
* **PHP Extensions:** (`check_extensions.php` suggests specific PHP extensions are required).

*This document should be updated as more technical details are discovered or decisions are made. It builds upon `projectbrief.md` and `systemPatterns.md`.*
