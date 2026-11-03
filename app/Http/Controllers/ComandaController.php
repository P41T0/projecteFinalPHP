<?php

namespace App\Http\Controllers;

use App\Models\Botiga;
use App\Models\Comanda;
use App\Models\Producte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ComandaController extends Controller
{
    public function afegir(Producte $producte)
    {
        //
        $comandesObertes = Auth()->user()->comandes->where('oberta');
        if ($comandesObertes->count() > 0) {
            $comanda = $comandesObertes->first();
        } else {
            $comanda = new Comanda;

            $comanda->usuari_id = Auth()->user()->id;
            $comanda->botiga_id = Botiga::query()->orderBy('poblacio')->firstOrFail()->id;
            $comanda->save();
        }
        $liniaComanda = $comanda->productes()->where('producte_id', $producte->id)->first();

        $novaQuantitat = 1;

        if ($liniaComanda) {
        } else {

            $comanda->productes()->attach($producte->id, ['quantitat' => $novaQuantitat]);
        }
        $preuTotal = 0;
        $numProds = 0;
        foreach ($comanda->productes as $prods) {
            $preuTotal += $prods->preu_unitari * $prods->pivot->quantitat;
            $numProds++;
        }
        $usuari = Auth()->user()->id;
        $botiga = Botiga::all();

        // dd($botiga);
        return view('compres.detall', ['numProductes' => $numProds, 'comanda' => $comanda, 'preuTotal' => $preuTotal, 'usuari' => $usuari, 'botigues' => $botiga]);
    }

    public function confirmar(Comanda $comanda)
    {
        abort_unless($comanda->usuari_id === Auth::id(), 403);

        $missatge = '';
        $preuTotal = 0;
        $botigues = Botiga::find($comanda->botiga_id);
        $botiga = $botigues->poblacio;
        $usuari = Auth()->user()->id;
        if ($comanda->oberta != 1) {
            foreach ($comanda->productes as $prods) {
                $preuTotal += $prods->preu_unitari * $prods->pivot->quantitat;
            }
            $missatge = 0;
        } elseif ($comanda->productes->isEmpty()) {
            $missatge = 1;
        } else {
            $comanda->oberta = 0;
            $comanda->save();

            foreach ($comanda->productes as $prods) {
                $preuTotal += $prods->preu_unitari * $prods->pivot->quantitat;
            }
            $missatge = 2;
        }

        return view('compres.confirma', compact('comanda', 'missatge', 'preuTotal', 'botiga'));
    }

    public function canviQuantitat(Request $request, Comanda $comanda)
    {
        abort_unless($comanda->usuari_id === Auth::id(), 403);

        if ($comanda->oberta == 1) {
            $validated = $request->validate([
                'productes' => ['required', 'array', 'min:1'],
                'productes.*' => ['required', 'integer', 'min:0', 'max:10'],
                'botiga' => ['required', 'uuid', 'exists:botiga,id'],
            ]);

            $producteIds = array_keys($validated['productes']);
            $attachedIds = $comanda->productes()
                ->whereIn('producte.id', $producteIds)
                ->pluck('producte.id')
                ->all();

            if (count($attachedIds) !== count($producteIds)) {
                throw ValidationException::withMessages([
                    'productes' => 'Un o més productes no pertanyen a aquesta comanda.',
                ]);
            }

            foreach ($validated['productes'] as $producteId => $quantitat) {
                if ($quantitat === 0) {
                    $comanda->productes()->detach($producteId);
                } else {
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
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Comanda $comanda)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comanda $comanda)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comanda $comanda)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comanda $comanda)
    {
        //
    }
}
