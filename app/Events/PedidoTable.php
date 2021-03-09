<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\PedidoProveedor;
use App\Proveedor;
use DB;

class PedidoTable implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $pedidos;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->pedidos = PedidoProveedor::select('pedidos_proveedores.*','proveedores.nombre')->join('proveedores','proveedores.id','=','pedidos_proveedores.idProveedor')->paginate(10);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('channel-PedidoTable');
    }
}
