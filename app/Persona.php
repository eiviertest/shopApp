<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'persona';
    protected $primaryKey = 'id';
    protected $fillable = ['nombre', 'tipo_documento', 'num_documento', 'direccion', 'telefono', 'email'];

    public function proveedor(){
        return $this->hasOne('App\Persona');
    }

    public function user(){
        return $this->hasOne('App\User');
    }
}
