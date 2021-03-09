<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\PagoProveedor;
use App\PedidoProveedor;
use DB;

class PagosTable implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $pagos;
    public $saldo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->pagos = PagoProveedor::where('idPedido',$id)->get();
        $this->saldo = PedidoProveedor::where('id',$id)->first();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('channel-PagosTable');
    }
}
