<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Seccio extends Model
{
    use HasUuids;

    protected $table = 'seccio';

    protected $fillable = [
        'nom',
        'nom_es',
        'nom_en',
    ];

    public function productes()
    {
        return $this->hasMany(Producte::class);
    }
}
