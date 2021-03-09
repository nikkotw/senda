<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Proveedor;
use DB;

class ProveedorTable implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $proveedor;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->proveedor = Proveedor::paginate(10);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('channel-ProveedorTable');
    }
}
