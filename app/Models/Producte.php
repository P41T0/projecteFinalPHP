<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Producte extends Model
{
    protected $table='Producte';
    public function Seccio(){
        return $this->belongsTo(Seccio::class);
    }
    public function Botiga(){
        return $this->belongsToMany(Botiga::class,'estoc_botiga')->withPivot('quantitat');
    }
    public function Comanda(){
        return $this->belongsToMany(Comanda::class,'linia_comanda')->withPivot('quantitat');
    }
}
