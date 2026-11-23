<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Comanda extends Model
{
    use HasUuids;

    protected $table = 'comanda';

    protected $fillable = [
        'usuari_id',
        'botiga_id',
        'oberta',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function usuari(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuari_id');
    }

    /**
     * @return BelongsToMany<Producte, $this, LiniaComanda>
     */
    public function productes(): BelongsToMany
    {
        // The linia_comanda pivot stores the quantity of each product in the order.
        return $this->belongsToMany(Producte::class, 'linia_comanda')
            ->using(LiniaComanda::class)
            ->withPivot('quantitat');
    }
}
