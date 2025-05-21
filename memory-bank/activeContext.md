# Active Context

## 1. Current Work Focus

* **What is the immediate task or feature being worked on?**
  * Resolving an "Internal Server Error" (SQLSTATE[42S22]: Column not found: 1054 Unknown column 'queue' in 'INSERT INTO') that occurs when an event listener (`NotifyFreelancersAboutNewJob`) is queued.
  * The root cause was identified as the application's main `jobs` table (for job postings) conflicting with the table name Laravel's database queue driver expects (`jobs` by default). The original migration for the queue's `jobs` table was repurposed.
* **Specific Goals for this Session:**
  * Correct currency display on admin jobs page.
  * Ensure admin job creation is logged in recent activity.
  * Previously: Confirm admin job creation and freelancer assignment process works without errors, and verify freelancer assignment notification logic. (These were largely completed).

## 2. Recent Changes & Decisions

* **What significant changes were made recently?**
  * **Event System for Logging (By Cline):**
    * Modified `app/Events/AdminJobPosted.php` to include `actionType`, `description`, and `model` properties for compatibility with `LogAdminActivity` listener.
    * Updated `app/Providers/EventServiceProvider.php` to map `AdminJobPosted` event to `LogAdminActivity` listener.
  * **Currency Display (By Cline):**
    * Updated `resources/views/admin/jobs/index.blade.php` to use `\App\Helpers\CurrencyHelper::formatZAR()` for budget and hourly rate.
  * **Verification (By Cline & User - Previous):**
    * Admin job creation and freelancer assignment process completed successfully without on-screen errors.
    * `SendFreelancerAssignmentNotification` listener verified.
    * `EventServiceProvider` verified for `JobAssigned` event.
  * **Controller Updates (By Cline - Previous):**
    * `JobAssignmentController` methods updated for redirects, fetching JobAssignment, and removing TODOs.
    * `JobAssignmentController@show` and `edit` methods updated to explicitly find `JobAssignment` by ID.
  * **View Updates (By Cline - Previous):**
    * `resources/views/admin/jobs/assignments/create.blade.php` and `show.blade.php` and `edit.blade.php` updated for correct routes and robust display.
  * **Route Update (By Cline - Previous):**
    * `routes/web.php` updated for `admin.jobs` resource.
  * **Model Updates (By Cline - Previous):**
    * `app/Models/Job.php`: Added `comments`, `createdByAdmin` relationships.
    * `app/Models/JobAssignment.php`: Corrected `timeLogs` to `HasManyThrough`.
  * **User Actions (Guided by Cline - Previous):**
    * Ensured `.env` file is correct for `DB_QUEUE_TABLE`.
    * User ran `php artisan config:clear`, `php artisan route:clear`, `php artisan migrate`.
* **Key decisions made during the last session:**
  * Implemented logging for admin job creation by modifying the existing `AdminJobPosted` event and `LogAdminActivity` listener.
  * Corrected currency display by utilizing the existing `CurrencyHelper`.
  * Previously: Confirmed freelancer notification logic; resolved multiple routing and relationship exceptions.

## 3. Next Steps & Pending Tasks

* **What are the immediate next steps based on the current focus?**
  * User to verify:
    * Currency display on `/admin/jobs` page.
    * That new job creation by admin appears in the "Recent Activity" log.
* **Broader pending tasks from `projectbrief.md` or `productContext.md` (and previous `activeContext`):**
  * Test the user creation flow by an admin to ensure the activity is logged and displayed correctly (this was pending before the queue error).
  * Expand logging to other admin actions.
  * User Registration (Client, Freelancer)
  * User Login & Authentication
  * User Profiles (Client, Freelancer)
  * Admin Dashboard (Basic functionality now enhanced with activity logging)

## 4. Active Considerations & Questions

* **Are there any open questions or points needing clarification?**
  * Will the user be able to run `php artisan migrate` successfully in their environment? (Limitations were noted previously).
* **Potential roadblocks or challenges anticipated?**
  * If `php artisan migrate` cannot be run easily, the fix will be stalled.

## 5. Important Patterns & Preferences (Observed or Stated)

* **Coding Style Preferences:**
  * Standard Laravel conventions
* **Architectural Preferences:**
  * Standard Laravel MVC patterns
  * Use of Blade components for views
  * Laravel Events & Listeners for decoupled actions.
  * Laravel Queues for background tasks.
* **Testing Preferences:**
  * (Not directly addressed in this task)
* **Tooling/Workflow Preferences:**
  * VS Code as IDE
  * Artisan commands for generating classes and running migrations.

## 6. Learnings & Insights from Current Work

* **What has been learned recently about the project, codebase, or tools?**
  * It's crucial to ensure that Laravel's default table names (like `jobs` for queues) do not conflict with application-specific table names if the default configurations are used.
  * If repurposing a default Laravel migration (like `create_jobs_table`), ensure that any features relying on the original schema (like database queues) are either reconfigured or have new, correct migrations provided.
  * The `config/queue.php` file allows specifying a custom table name for the database queue driver.

*This document is the most dynamic and should be updated frequently, ideally at the beginning and end of each development session. It links information from `productContext.md`, `systemPatterns.md`, and `techContext.md` to the immediate tasks at hand.*
