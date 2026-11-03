<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Botiga extends Model
{
    use HasUuids;

    protected $table = 'botiga';

    public function productes()
    {
        return $this->belongsToMany(Producte::class, 'estoc_botiga')
            ->using(EstocBotiga::class)
            ->withPivot('quantitat');
    }
}
