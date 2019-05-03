<?php

namespace App\Events;

use App\Episode;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EpisodeCreated implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $episode;
    public $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Episode $episode) { //Episode $episode
        $this->user = $user;
        $this->episode = $episode;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
//        return ['data-update'];
        return new PrivateChannel('data-update.' . $this->user->id);
    }
}
