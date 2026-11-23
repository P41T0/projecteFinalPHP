<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read EstocBotiga|LiniaComanda $pivot
 */
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

    /**
     * @return BelongsTo<Seccio, $this>
     */
    public function Seccio(): BelongsTo
    {
        return $this->belongsTo(Seccio::class);
    }

    /**
     * @return BelongsToMany<Botiga, $this, EstocBotiga>
     */
    public function Botiga(): BelongsToMany
    {
        // Expose per-store stock through the estoc_botiga pivot.
        return $this->belongsToMany(Botiga::class, 'estoc_botiga')
            ->using(EstocBotiga::class)
            ->withPivot('quantitat');
    }

    /**
     * @return BelongsToMany<Comanda, $this, LiniaComanda>
     */
    public function Comanda(): BelongsToMany
    {
        return $this->belongsToMany(Comanda::class, 'linia_comanda')
            ->using(LiniaComanda::class)
            ->withPivot('quantitat');
    }
}
