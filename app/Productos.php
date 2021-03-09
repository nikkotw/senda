<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    public $timestamps = false;

    protected $table = 'productoscombinacionescabecera';
    protected $primaryKey = 'IdProducto';
}
