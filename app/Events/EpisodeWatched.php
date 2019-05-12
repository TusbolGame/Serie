<?php

namespace App\Events;

use App\Episode;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EpisodeWatched implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $episode;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Episode $episode) { //Episode $episode
        $this->episode = $episode;
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
