<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comanda extends Model
{
    protected $table='Comanda';
    public function usuari()
    {
           return $this->belongsTo(User::class);
    }
    public function linies_comanda(){
        return $this->belongsToMany(Producte::class,"linia_comanda");
    }

}
