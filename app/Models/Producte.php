<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Producte extends Model
{
    use HasUuids;

    protected $table = 'producte';

    public function Seccio()
    {
        return $this->belongsTo(Seccio::class);
    }

    public function Botiga()
    {
        return $this->belongsToMany(Botiga::class, 'estoc_botiga')
            ->using(EstocBotiga::class)
            ->withPivot('quantitat');
    }

    public function Comanda()
    {
        return $this->belongsToMany(Comanda::class, 'linia_comanda')
            ->using(LiniaComanda::class)
            ->withPivot('quantitat');
    }
}
