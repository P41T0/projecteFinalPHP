<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Comanda extends Model
{
    use HasUuids;

    protected $table = 'comanda';

    protected $fillable = [
        'usuari_id',
        'botiga_id',
        'oberta',
    ];

    public function usuari()
    {
        return $this->belongsTo(User::class, 'usuari_id');
    }

    public function productes()
    {
        // The linia_comanda pivot stores the quantity of each product in the order.
        return $this->belongsToMany(Producte::class, 'linia_comanda')
            ->using(LiniaComanda::class)
            ->withPivot('quantitat');
    }
}
