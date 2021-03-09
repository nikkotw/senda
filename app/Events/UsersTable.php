<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\User;
use DB;

class UsersTable implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $users;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($page, $filter, $search)
    {
        if ($filter == 'nombre') {
            $this->users = User::where('nombre', 'like', '%'.$search.'%')->paginate(10,['*'],'page',$page);
        } else {
            if ($filter == 'cuil') {
                $this->users = User::where('cuil', 'like', '%'.$search.'%')->paginate(10,['*'],'page',$page);
            } else {
                $this->users  = User::paginate(10);
            }
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('channel-usersTable');
    }
}
