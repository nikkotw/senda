<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CarritoTransfer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $productos;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        $carrito = DB::table('detalle_transferstock')
        ->join('productoscombinacionescabecera', 'detalle_transferstock.idproducto', '=', 'productoscombinacionescabecera.idProducto')
        ->select('detalle_transferstock.*', 'productoscombinacionescabecera.Descripcion')
        ->get();
        $this->productos=$carrito;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('channel-CarritoTransfer');
    }
}
