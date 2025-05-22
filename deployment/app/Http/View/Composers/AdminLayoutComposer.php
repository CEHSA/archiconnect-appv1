<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;

class AdminLayoutComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $unreadMessagesCount = 0;
        if (Auth::guard('admin')->check()) {
            /** @var Admin $adminUser */
            $adminUser = Auth::guard('admin')->user();

            if (method_exists($adminUser, 'totalUnreadMessagesCount')) {
                $unreadMessagesCount = $adminUser->totalUnreadMessagesCount();
            }
        }
        $view->with('unreadMessagesCount', $unreadMessagesCount);
    }
}
