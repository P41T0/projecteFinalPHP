<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Comanda extends Model
{
    use HasUuids;

    protected $table = 'comanda';

    public function usuari()
    {
        return $this->belongsTo(User::class, 'usuari_id');
    }

    public function productes()
    {
        return $this->belongsToMany(Producte::class, 'linia_comanda')
            ->using(LiniaComanda::class)
            ->withPivot('quantitat');
    }
}
