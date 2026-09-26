<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use App\Models\Notification;
use App\Models\RolePermission; // Added RolePermission model import

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom Gate check for Super Admin and role_permissions table
        Gate::before(function ($user, $ability) {
            // Super Admin la sagle actions allow karnyasathi
            if ($user->role === 'Super Admin' || (method_exists($user, 'hasRole') && $user->hasRole('Super Admin'))) {
                return true;
            }

            // User cha role name fetch karne (Spatie va column kuthla hi asu dya)
            $userRole = $user->role ?? (method_exists($user, 'getRoleNames') ? $user->getRoleNames()->first() : null);

            if (!$userRole) {
                return false;
            }

            // Custom role_permissions table madhun check kara
            return RolePermission::where('role', $userRole)
                                 ->where('permission', $ability)
                                 ->exists();
        });

        // Bootstrap 5 pagination support
        Paginator::useBootstrapFive();

        // Global view composer for unread notifications
        View::composer('*', function ($view) {
            $unreadNotifications = Notification::whereNull('read_at')->get();
            $view->with([
                'unreadNotifications' => $unreadNotifications,
                'unreadCount' => $unreadNotifications->count()
            ]);
        });
    }
}