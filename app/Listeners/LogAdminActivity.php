<?php

namespace App\Listeners;

use App\Models\AdminActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogAdminActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event The event object, expected to have properties like admin, actionType, description, model.
     */
    public function handle(object $event): void
    {
        // Ensure an admin is authenticated and the event has the necessary data
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            // Or log a warning, but don't create an activity log if no admin is found
            return;
        }

        // Attempt to get data from the event.
        // This is a simplified approach. A more robust solution would use an interface/contract
        // for events that should be logged, ensuring these properties/methods exist.
        $actionType = property_exists($event, 'actionType') ? $event->actionType : 'unknown_action';
        $description = property_exists($event, 'description') ? $event->description : 'An unspecified admin action occurred.';
        $loggableModel = property_exists($event, 'model') ? $event->model : null;

        AdminActivityLog::create([
            'admin_id' => $admin->id,
            'action_type' => $actionType,
            'description' => $description,
            'loggable_id' => $loggableModel ? $loggableModel->id : null,
            'loggable_type' => $loggableModel ? get_class($loggableModel) : null,
        ]);
    }
}
