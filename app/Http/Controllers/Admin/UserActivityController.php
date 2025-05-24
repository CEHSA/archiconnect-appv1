<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = UserActivity::with('user')->orderBy('created_at', 'desc');

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action') && $request->action != '') {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $activities = $query->paginate(10);

        return view('admin.users.activity.index', compact('activities'));
    }

    public function exportCsv(Request $request)
    {
        $query = UserActivity::with('user')->orderBy('created_at', 'desc');

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action') && $request->action != '') {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $activities = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_activities.csv"',
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['User', 'Action', 'Details', 'IP Address', 'Timestamp', 'Session Duration (minutes)']);

            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->user->name ?? 'N/A',
                    $activity->action,
                    $activity->details,
                    $activity->ip_address,
                    $activity->created_at,
                    $activity->session_duration_minutes,
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
