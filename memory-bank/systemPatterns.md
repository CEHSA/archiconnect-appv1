# System Patterns

## 1. Overall Architecture

*   **Describe the high-level architecture of the system.**
    *   (e.g., Monolithic, Microservices, Layered, MVC, MVVM, etc.)
    *   **Current Understanding:** Based on the file structure (Laravel framework conventions like `app/Http/Controllers`, `routes/web.php`, `resources/views`), the application appears to follow a **Model-View-Controller (MVC)** architecture.
    *   **Diagram (Optional - Mermaid):**
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

*   **List major technical decisions made and why.**
    *   **Framework:** Laravel (PHP) - Inferred from file structure.
        *   *Justification (Assumed):* Rapid development, strong community, built-in features (ORM, routing, templating), security considerations.
    *   **Database:** (To be determined - likely MySQL/PostgreSQL as common with Laravel, or SQLite for local dev)
        *   *Justification:* (To be added once confirmed)
    *   **Frontend:** Blade templates (`resources/views`) - Standard for Laravel. Potentially with JavaScript (e.g., Alpine.js, Vue.js, or vanilla JS - see `resources/js`).
        *   *Justification (Assumed):* Seamless integration with Laravel backend, server-side rendering benefits.
    *   **CSS Styling:** (To be determined - e.g., Tailwind CSS, Bootstrap, custom CSS - check `resources/css` and `tailwind.config.js` if present). `tailwind.config.js` is present, indicating **Tailwind CSS**.
        *   *Justification (Assumed):* Utility-first approach for rapid UI development.
    *   **Job Queue / Background Tasks:** (To be determined - Laravel Queues are likely if asynchronous tasks are needed, e.g., sending emails, processing uploads).
        *   *Justification:* (To be added if used)

## 3. Core Design Patterns in Use

*   **MVC (Model-View-Controller):** As mentioned above, fundamental to Laravel.
    *   **Models (`app/Models`):** Represent data and business logic (e.g., `User.php`, `Job.php`). Interact with the database via Eloquent ORM.
    *   **Views (`resources/views`):** Handle presentation logic (Blade templates).
    *   **Controllers (`app/Http/Controllers`):** Handle user requests, interact with models, and select views to render.
*   **Eloquent ORM:** Laravel's Object-Relational Mapper for database interactions.
*   **Middleware (`app/Http/Middleware`):** Used for filtering HTTP requests (e.g., authentication, CSRF protection).
*   **Service Providers (`app/Providers`):** Central place to configure and bootstrap Laravel services.
*   **Events & Listeners (`app/Events`, `app/Listeners`):** For decoupling parts of the application (e.g., `JobPosted` event and `NotifyFreelancers` listener). This pattern is evident from the file list.
*   **Dependency Injection / Service Container:** Laravel's IoC container manages class dependencies.
*   **Routing (`routes/web.php`, `routes/api.php`):** Defines how URLs map to controllers and actions.
*   **Request Validation (`app/Http/Requests`):** Form Requests for validating incoming data.

## 4. Component Relationships & Critical Paths

*   **User Authentication Flow:**
    *   Request -> Routes -> Auth Controllers -> Middleware (auth) -> User Model -> Database
*   **Job Posting Flow:**
    *   Client Request (Create Job Form) -> Routes -> `JobController@create/store` -> `JobRequest` (Validation) -> `Job` Model (Save to DB) -> `JobPosted` Event -> Listeners (e.g., `NotifyFreelancersAboutNewJob`) -> View (Success message)
*   **Proposal Submission Flow:**
    *   Freelancer Request (Submit Proposal Form) -> Routes -> `ProposalController@store` -> `ProposalRequest` (Validation) -> `Proposal` Model (Save to DB, associate with Job & Freelancer) -> Event (e.g., `ProposalSubmitted`) -> Listeners -> View
*   **Messaging System (High-Level):**
    *   User A sends message -> `MessageController@store` -> `Message` Model (Save to DB, associate with Conversation, Sender, Receiver) -> Event (`NewMessage`) -> Listener (Real-time notification via WebSockets/Pusher if implemented, or email notification) -> User B receives message.

## 5. Data Flow & Management

*   **Primary Data Storage:** Relational Database (details TBD).
*   **Data Integrity:** Maintained through database constraints, Eloquent model relationships, and validation rules in Form Requests.
*   **Caching Strategy:** (To be determined - Laravel supports various caching backends like Redis, Memcached, file-based).
*   **File Storage:** (`storage/app`) Local file system for user uploads (e.g., profile pictures, project files). Could be configured to use cloud storage (e.g., AWS S3).

## 6. Error Handling & Logging

*   **Error Handling:** Laravel's built-in exception handler. Custom exception handlers can be created.
*   **Logging:** (`storage/logs/laravel.log`) Laravel's Monolog integration for logging application events and errors.

*This document should be updated as the system evolves and new patterns are adopted or existing ones are refined. It builds upon `projectbrief.md`.*
