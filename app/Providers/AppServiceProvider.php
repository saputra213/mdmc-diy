<?php

namespace App\Providers;

use App\Models\DisasterEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

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
        if (app()->runningInConsole()) {
            return;
        }

        if (!Auth::check()) {
            return;
        }

        if (!session()->has('admin_disaster_event_id')) {
            $fallback = DisasterEvent::active()->orderByDesc('updated_at')->value('id')
                ?: DisasterEvent::query()->orderByDesc('id')->value('id');

            if ($fallback) {
                session(['admin_disaster_event_id' => (int) $fallback]);
            }
        }

        $eventId = request()->query('event_id');
        if ($eventId === null || $eventId === '') {
            return;
        }

        if (!is_numeric($eventId)) {
            return;
        }

        $eventId = (int) $eventId;
        if ($eventId <= 0) {
            return;
        }

        if ((int) session('admin_disaster_event_id') === $eventId) {
            return;
        }

        if (!DisasterEvent::query()->whereKey($eventId)->exists()) {
            return;
        }

        session(['admin_disaster_event_id' => $eventId]);
    }
}
