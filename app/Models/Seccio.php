<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seccio extends Model
{
    protected $table = 'seccio';

    public function productes()
    {
        return $this->hasMany(Producte::class);
    }
}
