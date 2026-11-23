<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Botiga extends Model
{
    use HasUuids;

    protected $table = 'botiga';

    protected $fillable = [
        'nom',
        'poblacio',
    ];

    /**
     * @return BelongsToMany<Producte, $this, EstocBotiga>
     */
    public function productes(): BelongsToMany
    {
        // The estoc_botiga pivot stores the available quantity for each product.
        return $this->belongsToMany(Producte::class, 'estoc_botiga')
            ->using(EstocBotiga::class)
            ->withPivot('quantitat');
    }
}
