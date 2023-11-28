<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccio extends Model
{
    protected $table='Seccio';
    
    public function productes(){
        return $this->hasMany(Producte::class);
    }
}
