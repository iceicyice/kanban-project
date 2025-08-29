<?php

namespace App\Filament\Topbar;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Notifications extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $open = false;

    protected $listeners = ['refreshNotifications',];

    public string $componentName = 'topbar-notifications';

    public function mount()
    {
        $user = Auth::user();
        $this->notifications = $user->unreadNotifications->toArray();
        $this->unreadCount = count($this->notifications);
    }

    public function refreshNotifications()
    {
        $user = auth()->user();
        $this->notifications = $user->unreadNotifications->toArray();
        $this->unreadCount = count($this->notifications);
    }


    public function toggle()
    {
        $this->open = !$this->open;

        // If opening, refresh notifications
        if ($this->open) {
            $this->refreshNotifications();
        }
    }

    public function addNotification($notification)
    {
        array_unshift($this->notifications, $notification);
        $this->unreadCount++;
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->unreadNotifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
            $this->notifications = array_filter($this->notifications, fn($n) => $n['id'] !== $id);
            $this->unreadCount--;
        }
    }

    // Mark all notifications as read
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        $this->refreshNotifications();
    }

    public function render()
    {
        return view('filament.topbar.notifications');
    }
}
