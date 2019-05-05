<?php

namespace App\Events;

use App\Show;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Auth;

class ShowUpdated implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $show;
    public $currentShowNumber;
    public $totalShowNumber;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Show $show, $currentShowNumber, $totalShowNumber) {
        $this->show = $show;
        $this->currentShowNumber = $currentShowNumber;
        $this->totalShowNumber = $totalShowNumber;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('data-update.' . Auth::user()->id);
    }
}
