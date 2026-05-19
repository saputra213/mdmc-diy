<?php

namespace App\Livewire;

use App\Models\DisasterEvent;
use Livewire\Component;

class AdminDisasterEventFilter extends Component
{
    public $selectedEventId;

    public function mount(): void
    {
        $urlValue = request()->query('event_id');
        if ($urlValue !== null && $urlValue !== '' && is_numeric($urlValue)) {
            $candidate = (int) $urlValue;
            if ($candidate > 0 && DisasterEvent::query()->whereKey($candidate)->exists()) {
                $this->selectedEventId = $candidate;
                session(['admin_disaster_event_id' => $candidate]);
                return;
            }
        }

        $sessionValue = session('admin_disaster_event_id');
        if ($sessionValue) {
            $this->selectedEventId = (int) $sessionValue;
            return;
        }

        $active = DisasterEvent::active()->orderByDesc('updated_at')->first();
        if ($active) {
            $this->selectedEventId = $active->id;
            session(['admin_disaster_event_id' => $active->id]);
            return;
        }

        $first = DisasterEvent::orderByDesc('id')->first();
        $this->selectedEventId = $first?->id;
        if ($this->selectedEventId) {
            session(['admin_disaster_event_id' => $this->selectedEventId]);
        }
    }

    public function updatedSelectedEventId($value): void
    {
        $value = $value ? (int) $value : null;
        session(['admin_disaster_event_id' => $value]);
        $this->dispatch('disaster-event-changed');
        $this->dispatch('admin-event-changed', eventId: $value);
    }

    public function render()
    {
        $events = DisasterEvent::orderByRaw("case when status = 'active' then 0 else 1 end")
            ->orderByDesc('updated_at')
            ->get();

        return view('livewire.admin-disaster-event-filter', [
            'events' => $events,
        ]);
    }
}
