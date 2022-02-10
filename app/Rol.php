<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'id';
    protected $fillable = ['nombre', 'descripcion', 'condicion'];
    public $timestamps = false;

    public function users(){
        return $this->hasMany('App\User');
    }
}
