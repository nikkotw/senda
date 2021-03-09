<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Venta;
use DB;

class VentaFinalizada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ventas;
    public $ventasAutorizadas;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->ventas = Venta::where("estado","=",0)->get();
        $this->ventasAutorizadas = Venta::where("estado","=",2)->get();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('channel-VentaFinalizada');
    }
}
