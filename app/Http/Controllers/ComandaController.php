<?php

namespace App\Http\Controllers;

use App\Models\Botiga;
use App\Models\Comanda;
use App\Models\Producte;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ComandaController extends Controller
{
    public function afegir(Producte $producte): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        // Reuse the authenticated user's open order, or create one if it does not exist.
        $comanda = $user->comandes()->where('oberta', true)->first();
        if (! $comanda) {
            $comanda = new Comanda;

            $comanda->usuari_id = $user->id;
            // Use the first store alphabetically by population as the default pickup location.
            $comanda->botiga_id = Botiga::query()->orderBy('poblacio')->firstOrFail()->id;
            $comanda->save();
        }
        $liniaComanda = $comanda->productes()->where('producte_id', $producte->id)->first();

        $novaQuantitat = 1;

        if (! $liniaComanda) {
            // A new product starts in the order with quantity one.
            $comanda->productes()->attach($producte->id, ['quantitat' => $novaQuantitat]);
        }
        // Calculate the cart summary from the unit price and the pivot quantity.
        $preuTotal = 0;
        $numProds = 0;
        foreach ($comanda->productes as $prods) {
            $preuTotal += $prods->preu_unitari * $prods->pivot->quantitat;
            $numProds++;
        }
        $usuari = $user->id;
        $botiga = Botiga::all();

        return view('compres.detall', ['numProductes' => $numProds, 'comanda' => $comanda, 'preuTotal' => $preuTotal, 'usuari' => $usuari, 'botigues' => $botiga]);
    }

    public function confirmar(Comanda $comanda): View
    {
        // Orders are private: a logged-in user can only confirm their own order.
        abort_unless($comanda->usuari_id === Auth::id(), 403);

        $missatge = '';
        $preuTotal = 0;
        $botiga = Botiga::findOrFail($comanda->botiga_id)->poblacio;
        if ($comanda->oberta != 1) {
            // A closed order is already confirmed and must not be changed again.
            foreach ($comanda->productes as $prods) {
                $preuTotal += $prods->preu_unitari * $prods->pivot->quantitat;
            }
            $missatge = 0;
        } elseif ($comanda->productes->isEmpty()) {
            $missatge = 1;
        } else {
            // Closing the order represents the confirmation of the purchase.
            $comanda->oberta = false;
            $comanda->save();

            foreach ($comanda->productes as $prods) {
                $preuTotal += $prods->preu_unitari * $prods->pivot->quantitat;
            }
            $missatge = 2;
        }

        return view('compres.confirma', compact('comanda', 'missatge', 'preuTotal', 'botiga'));
    }

    public function canviQuantitat(Request $request, Comanda $comanda): RedirectResponse
    {
        // Prevent users from modifying another user's order.
        abort_unless($comanda->usuari_id === Auth::id(), 403);

        if ($comanda->oberta == 1) {
            /** @var array{productes: array<array-key, int|string>, botiga: string} $validated */
            $validated = $request->validate([
                'productes' => ['required', 'array', 'min:1'],
                'productes.*' => ['required', 'integer', 'min:0', 'max:10'],
                'botiga' => ['required', 'uuid', 'exists:botiga,id'],
            ]);
            $productes = $validated['productes'];

            $producteIds = array_keys($productes);
            $attachedIds = $comanda->productes()
                ->whereIn('producte.id', $producteIds)
                ->pluck('producte.id')
                ->all();

            // Reject submitted products that are not actually lines of this order.
            if (count($attachedIds) !== count($productes)) {
                throw ValidationException::withMessages([
                    'productes' => 'Un o més productes no pertanyen a aquesta comanda.',
                ]);
            }

            foreach ($productes as $producteId => $quantitat) {
                $quantitat = (int) $quantitat;
                if ($quantitat === 0) {
                    // Quantity zero removes the product from the pivot table.
                    $comanda->productes()->detach($producteId);
                } else {
                    // Update only the quantity stored in linia_comanda.
                    $comanda->productes()->updateExistingPivot($producteId, ['quantitat' => $quantitat]);
                }
            }
            $comanda->botiga_id = $validated['botiga'];
            $comanda->save();

            if ($comanda->productes->isEmpty()) {
                return redirect()->route('inici');
            }

            return redirect()->route('comprarNP');
        } else {
            return redirect()->route('confirma.compres', $comanda->id);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): void {}

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void {}

    /**
     * Display the specified resource.
     */
    public function show(Comanda $comanda): void {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comanda $comanda): void {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comanda $comanda): void {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comanda $comanda): void {}
}
