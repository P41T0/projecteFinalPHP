<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Producte extends Model
{
    public function Seccio(){
        return $this->belongsTo(Seccio::class);
    }
    public function Botiga(){
        return $this->belongsToMany(Botiga::class,'estoc_botiga');
    }
    public function Comanda(){
        return $this->belongsToMany(Comanda::class,'linia_comanda');
    }
}
