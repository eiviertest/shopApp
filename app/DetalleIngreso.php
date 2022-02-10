<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleIngreso extends Model
{
    protected $table = 'detalle_ingreso';
    protected $primaryKey = 'id';
    protected $fillable = [
        'idingreso', 
        'idarticulo ', 
        'cantidad', 
        'precio'
    ];
    public $timestamps = false;
}
