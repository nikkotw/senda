<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagoProveedor extends Model
{
    protected $table = "pagos_pedidos_proveedores";
    public $timestamps = false;
}
