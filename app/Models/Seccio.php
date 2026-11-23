<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seccio extends Model
{
    use HasUuids;

    protected $table = 'seccio';

    protected $fillable = [
        'nom',
        'nom_es',
        'nom_en',
    ];

    /**
     * @return HasMany<Producte, $this>
     */
    public function productes(): HasMany
    {
        return $this->hasMany(Producte::class);
    }
}
