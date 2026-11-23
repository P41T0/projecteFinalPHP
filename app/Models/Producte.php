<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Producte extends Model
{
    use HasUuids;

    protected $table = 'producte';

    protected $fillable = [
        'nom',
        'nom_es',
        'nom_en',
        'descripcio',
        'descripcio_es',
        'descripcio_en',
        'preu_unitari',
        'foto',
        'seccio_id',
        'mostra_prod',
    ];

    public function Seccio()
    {
        return $this->belongsTo(Seccio::class);
    }

    public function Botiga()
    {
        // Expose per-store stock through the estoc_botiga pivot.
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
