# Active Context

## 1. Current Work Focus

* **What is the immediate task or feature being worked on?**
    * **Refactor Messaging System for Group Chat & Full Notification Flow (Phase 1 Complete):**
        * **Goal:** Overhaul the messaging system to support group conversations linked to jobs/assignments, implement admin approval for freelancer messages, and ensure all participants are notified appropriately, including notifying senders when their messages are approved or rejected.
        * **Files Affected/Created (Summary):**
            * Database migration for `conversations` table (removed participant1/2_id).
            * `app/Models/Conversation.php` (refactored for group participants via pivot table).
            * `app/Http/Controllers/Client/MessageController.php` (refactored, dispatches `ClientMessageSent`).
            * `app/Http/Controllers/Freelancer/MessageController.php` (refactored, sets messages to `pending_review`, attachment paths updated).
            * `app/Http/Controllers/Admin/MessageController.php` (refactored, dispatches `AdminMessageSent`, handles message approval, attachment paths updated).
            * `app/Http/Controllers/Admin/JobApplicationController.php` & `app/Http/Controllers/Admin/JobAssignmentController.php` (updated to sync conversation participants on assignment).
            * Events: `ClientMessageSent`, `AdminMessageSent`. (Existing `FreelancerMessageCreated`, `MessageApprovedByAdmin`, `MessageRejectedByAdmin` also part of the flow).
            * Listeners: `NotifyParticipantsOfClientMessage`, `NotifyParticipantsOfAdminMessage`, `NotifyParticipantsOfApprovedMessage` (refactored to notify sender and other participants separately), `NotifySenderOfRejectedMessage` (existing, verified).
            * Notifications: `NewMessageFromClientNotification`, `NewMessageFromAdminNotification`, `NewApprovedMessageInConversationNotification`, `YourMessageWasApprovedNotification` (new).
            * `app/Providers/EventServiceProvider.php` (updated with new events/listeners).
        * **Outcome:** Core backend for group messaging established. Client and Admin messages notify other participants. Freelancer messages require admin approval; approval notifies sender (with `YourMessageWasApprovedNotification`) and other participants (with `NewApprovedMessageInConversationNotification`); rejection notifies sender. Attachment paths updated. Participant syncing on job assignment implemented.
    * **Next Task:** Verify attachment storage path implementation and ensure `FileHelper` (if used for URL generation) is compatible. Then, UI enhancements for messaging or other pending tasks.
* **Previous Work (This Session):**
    * Client View for Job Applications.
    * Clarification of "Proposals" vs. "Job Applications".
    * Client Profile Notification Preference.
    * Job Application System (Submission, Admin Management, Notifications).

## 2. Recent Changes & Decisions

* **What significant changes were made recently (This Session)?**
    * **Completed Messaging System Refactor (Phase 1):**
        * Full refactor for group chat, including DB, Model, Controllers.
        * Comprehensive event-listener-notification system for client, admin, and freelancer messages (including approval/rejection flow).
        * Ensured conversation participants are synced on job assignment.
        * Updated attachment storage paths.
        * **Impact:** Core backend for new messaging workflow established.
    * (Previous changes detailed in prior `activeContext.md` versions)

## 3. Next Steps & Pending Tasks

* **For Enhanced Messaging System (Phase 2 & Refinements):**
    * **Attachment Storage Path Verification & URL Generation:**
        * Confirm `store($storagePath, 'public')` correctly creates/uses `public/storage/ArchiAxis/Job_X/chat_thread/`.
        * Verify public symlink and URL generation for attachments in views.
    * **UI Enhancements:** WhatsApp styling, Emojis.
    * **Participant Management Review:**
        * Review conversation creation points (e.g., when a client posts a job, should a conversation be auto-created with admins?).
        * Admin roles in conversations: Current logic adds *all* admins to some conversations. Refine if needed.
* **Remaining from Job Application System:**
    * Update action URLs in notifications if new specific views are created.
    * Thorough testing of the 'accepted_for_assignment' flow.
* **Broader pending tasks:** (As listed in previous `activeContext.md`)

## 4. Active Considerations & Questions

* **Conversation Context:** How to handle multiple conversation threads for a single Job/Assignment if needed.
* **Real-time Updates:** Future consideration.

## 5. Important Patterns & Preferences (Observed or Stated)

* Event-driven notifications.
* WhatsApp-style messaging.
* Specific folder structure for attachments.

## 6. Learnings & Insights from Current Work

* Refactoring core systems like messaging has wide-ranging impacts.
* Clear definition of participant roles and notification flows is key for group chat.
* Ensuring all user types (Client, Freelancer, Admin) have appropriate message handling logic in their respective controllers is crucial.

*This document is the most dynamic and should be updated frequently.*
