# System Patterns

## 1. Overall Architecture

* **Describe the high-level architecture of the system.**
  * (e.g., Monolithic, Microservices, Layered, MVC, MVVM, etc.)
  * **Current Understanding:** Based on the file structure (Laravel framework conventions like `app/Http/Controllers`, `routes/web.php`, `resources/views`), the application appears to follow a **Model-View-Controller (MVC)** architecture.
  * **Diagram (Optional - Mermaid):**

        ```mermaid
        graph LR
            User[User/Browser] --> R[Router: web.php, api.php];
            R --> C[Controllers: app/Http/Controllers];
            C --> M[Models: app/Models];
            M --> DB[(Database)];
            C --> V[Views: resources/views];
            V --> User;
            C --> S[Services/Helpers: app/Helpers, app/Services];
            S --> M;
        ```

## 2. Key Technical Decisions & Justifications

* **List major technical decisions made and why.**
  * **Framework:** Laravel (PHP) - Inferred from file structure.
    * *Justification (Assumed):* Rapid development, strong community, built-in features (ORM, routing, templating), security considerations.
  * **Database:** (To be determined - likely MySQL/PostgreSQL as common with Laravel, or SQLite for local dev)
    * *Justification:* (To be added once confirmed)
  * **Frontend:** Blade templates (`resources/views`) - Standard for Laravel. Potentially with JavaScript (e.g., Alpine.js, Vue.js, or vanilla JS - see `resources/js`).
    * *Justification (Assumed):* Seamless integration with Laravel backend, server-side rendering benefits.
  * **CSS Styling:** `tailwind.config.js` is present, indicating **Tailwind CSS**.
    * *Justification (Assumed):* Utility-first approach for rapid UI development.
    * *Standard UI Theme (Adopted May 2025):* A specific light theme is being implemented across all application views (Admin, Client, Freelancer). Key characteristics include:
      * White content cards with a distinct green border (e.g., `border-green-300`).
      * Teal/cyan accents for primary actions (e.g., `bg-cyan-700 text-white` for buttons) and table headers.
      * Consistent light backgrounds for content areas, removing dark-theme variants from these sections.
  * **Job Queue / Background Tasks:** (To be determined - Laravel Queues are likely if asynchronous tasks are needed, e.g., sending emails, processing uploads).
    * *Justification:* (To be added if used)

## 3. Core Design Patterns in Use

* **MVC (Model-View-Controller):** As mentioned above, fundamental to Laravel.
  * **Models (`app/Models`):** Represent data and business logic (e.g., `User.php`, `Job.php`). Interact with the database via Eloquent ORM.
  * **Views (`resources/views`):** Handle presentation logic (Blade templates).
  * **Controllers (`app/Http/Controllers`):** Handle user requests, interact with models, and select views to render.
* **Eloquent ORM:** Laravel's Object-Relational Mapper for database interactions.
* **Middleware (`app/Http/Middleware`):** Used for filtering HTTP requests (e.g., authentication, CSRF protection).
* **Service Providers (`app/Providers`):** Central place to configure and bootstrap Laravel services.
* **Events & Listeners (`app/Events`, `app/Listeners`):** For decoupling parts of the application (e.g., `JobPosted` event and `NotifyFreelancers` listener). This pattern is evident from the file list.
* **Dependency Injection / Service Container:** Laravel's IoC container manages class dependencies.
* **Routing (`routes/web.php`, `routes/api.php`):** Defines how URLs map to controllers and actions.
* **Request Validation (`app/Http/Requests`):** Form Requests for validating incoming data.
* **UI/UX Styling Conventions (Standard Light Theme):**
  * **Main Content Card:** `bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300`.
  * **Page Header Slot (e.g., `h2` in `x-admin-layout` or `x-app-layout`):** `font-semibold text-xl text-gray-800 leading-tight` (dark text on light layout background).
  * **Primary Action Buttons (e.g., "Create New", "Manage Tasks"):** `inline-flex items-center px-4 py-2 bg-cyan-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-600 focus:bg-cyan-600 active:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150`.
  * **Secondary/Back Buttons (e.g., "Back to Dashboard"):** `bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded text-xs uppercase tracking-widest`.
  * **Edit/Contextual Buttons (e.g., "Edit Assignment Details"):** `bg-yellow-500 hover:bg-yellow-400 text-gray-800` (plus standard button classes).
  * **Table Headers (`thead`):** `bg-cyan-700`.
  * **Table Header Cells (`th`):** `px-6 py-3 text-left text-xs font-semibold text-white`. (Note: `uppercase` and `tracking-wider` were removed from previous dark theme for a cleaner look).
  * **Table Body (`tbody`):** `bg-white divide-y divide-gray-200`.
  * **Table Data Cells (`td`):** `px-6 py-4 whitespace-nowrap text-sm`. Text color typically `text-gray-900` (primary data like titles) or `text-gray-700` (secondary data).
  * **Action Links in Tables:** `text-blue-600 hover:text-blue-700` (for View/Edit), `text-red-600 hover:text-red-700` (for Delete).
  * **Data Display in Detail Views (Label-Over-Value):**
    * Labels (`<dt>`): `block text-sm font-medium text-gray-500`.
    * Values (`<dd>`): `mt-1 text-sm text-gray-900`.
  * **Tab Navigation:**
    * Active Tab: `border-teal-500 text-teal-600`.
    * Inactive Tab: `border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300`.
  * **General Text:** Default to standard light theme text colors (e.g., `text-gray-900` for headings, `text-gray-700` or `text-gray-600` for body copy). Dark theme variants (`dark:...`) are to be removed from re-themed sections of the application.

## 4. Component Relationships & Critical Paths

* **User Authentication Flow:**
  * Request -> Routes -> Auth Controllers -> Middleware (auth) -> User Model -> Database
* **Job Posting Flow:**
  * Client Request (Create Job Form) -> Routes -> `JobController@create/store` -> `JobRequest` (Validation) -> `Job` Model (Save to DB) -> `JobPosted` Event -> Listeners (e.g., `NotifyFreelancersAboutNewJob`) -> View (Success message)
* **Proposal Submission Flow:**
  * Freelancer Request (Submit Proposal Form) -> Routes -> `ProposalController@store` -> `ProposalRequest` (Validation) -> `Proposal` Model (Save to DB, associate with Job & Freelancer) -> Event (e.g., `ProposalSubmitted`) -> Listeners -> View
* **Messaging System (High-Level):**
  * User A sends message -> `MessageController@store` -> `Message` Model (Save to DB, associate with Conversation, Sender, Receiver) -> Event (`NewMessage`) -> Listener (Real-time notification via WebSockets/Pusher if implemented, or email notification) -> User B receives message.
* **Work Submission & Review Flow (High-Level):**
  * **Freelancer Submits:** Freelancer Request (Submit Work Form) -> `Freelancer\WorkSubmissionController@store` -> `WorkSubmission` Model (Create, set status to `submitted_for_admin_review`, store file) -> `FreelancerWorkSubmitted` Event -> Listener (Notify Admin).
  * **Admin Reviews:** Admin views submission (`Admin\WorkSubmissionController@show`) -> Admin adds comment (optional screenshot) via `JobCommentController@store` (linked to `WorkSubmission`) -> Admin updates submission status via `Admin\WorkSubmissionController@update`:
    * If "Request Revisions": Status -> `admin_revision_requested` -> `WorkSubmissionReviewedByAdmin` Event -> Listener (Notify Freelancer).
    * If "Submit to Client": Status -> `pending_client_review` -> `WorkSubmissionSubmittedToClient` Event -> Listener (Notify Client).
  * **Client Reviews:** Client views submission (`Client\WorkSubmissionController@show`) -> Client adds comment (optional screenshot) via `JobCommentController@store` (linked to `WorkSubmission`) -> `JobCommentCreated` Event -> Listener (`NotifyAdminAndFreelancerOfClientComment`). Client updates submission status via `Client\WorkSubmissionController@update`:
    * If "Approve": Status -> `approved_by_client` -> `ClientWorkSubmissionReviewed` Event -> Listener (Notify Admin/Freelancer).
    * If "Request Revisions": Status -> `client_revision_requested` -> `ClientWorkSubmissionReviewed` Event -> Listener (Notify Admin/Freelancer).
* **Job Acceptance Flow (Phase 1):**
  * Freelancer clicks "Accept Job" on `freelancer.jobs.show` for an 'open' job.
  * POST request to `Freelancer\JobController@acceptJob`.
  * Controller:
    * Verifies job status.
    * Creates `JobAssignment` (status: `pending_admin_approval`, `freelancer_id`: current user, `client_id`: job owner).
    * Updates `Job` status to `pending_admin_approval`.
    * Dispatches `JobAcceptanceRequested` event (with `Job`, `JobAssignment`, `User` (freelancer)).
  * `NotifyAdminOfJobAcceptanceRequest` listener (queued):
    * Fetches admins.
    * (TODO: Sends `AdminJobAcceptanceRequestedNotification` to admins).
  * View (`freelancer.jobs.show`) updates to show "Awaiting admin approval".
* **Freelancer-Admin Job Specific Messaging Flow ("Have Questions?"):**
  * Freelancer clicks "Have Questions?" on `freelancer.jobs.show`.
  * GET request to `Freelancer\MessageController@createAdminMessageForJob` (passes `Job` model).
  * View `freelancer.messages.create-admin-for-job` is rendered with a form.
  * Freelancer submits form (POST to `Freelancer\MessageController@storeAdminMessageForJob`).
  * Controller:
    * Validates input.
    * Finds/creates `Conversation` (linked to freelancer, an admin, and the `Job`).
    * Creates `Message` (status: `pending`).
    * Dispatches `FreelancerMessageCreated` event.
  * `NotifyAdminsOfPendingMessage` listener (existing):
    * (Handles notifying admin of the new message).
  * Redirects freelancer back to `freelancer.jobs.show` with success message.

## 5. Data Flow & Management

* **Primary Data Storage:** Relational Database (details TBD).
* **Data Integrity:** Maintained through database constraints, Eloquent model relationships, and validation rules in Form Requests.
* **Caching Strategy:** (To be determined - Laravel supports various caching backends like Redis, Memcached, file-based).
* **File Storage:** (`storage/app`) Local file system for user uploads (e.g., profile pictures, project files). Could be configured to use cloud storage (e.g., AWS S3).

## 6. Error Handling & Logging

* **Error Handling:** Laravel's built-in exception handler. Custom exception handlers can be created.
* **Logging:** (`storage/logs/laravel.log`) Laravel's Monolog integration for logging application events and errors.
* **Defensive Eager Loading & Data Validation in Controllers:** To prevent "Attempt to read property on null" errors (often leading to 500 errors), controllers should:
  * Ensure that necessary related models and their nested relationships are correctly loaded (e.g., `Job::with(['user.clientProfile'])`).
  * For index/browse pages, use `whereHas` clauses to filter out records that don't have essential related data (e.g., `Job::whereHas('user.clientProfile')`).
  * For `show` pages (where the model is route-model bound), explicitly check for the existence of critical related data after loading and `abort(404)` or handle gracefully if not found (e.g., `if (!$job->user || !$job->user->clientProfile) { abort(404); }`). This pattern was applied to `JobController@browse` and `FreelancerJobController@show` to resolve issues with missing `clientProfile` data.

*This document should be updated as the system evolves and new patterns are adopted or existing ones are refined. It builds upon `projectbrief.md`.*
