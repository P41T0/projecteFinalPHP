<?php

namespace App\Http\Controllers;

use App\Models\Comanda;
use App\Models\Producte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComandaController extends Controller
{
    public function afegir(Producte $producte)
    {
        //
        $comandesObertes=Auth()->user()->comandes->where('oberta');
        if ($comandesObertes->count()>0){
            echo "Hi ha comandes obertes";
            $comanda = $comandesObertes->first();            
        }else{
            echo "no hi ha comandes obertes";
            $comanda = new Comanda;

            $comanda->usuari_id=Auth()->user()->id;
            $comanda->botiga_id=1;
            $comanda->save();
        }
        echo $comanda;
        $liniaComanda = $comanda->linies_comanda()->where('producte_id', $producte->id)->first();
        print($liniaComanda->quantitat);
        // Cantidad inicial
        $novaQuantitat = 260;
    
        if ($liniaComanda) {
            // Si el producto ya está en la comanda, actualiza la cantidad
            $novaQuantitatTotal = $liniaComanda->quantitat + $novaQuantitat;
            $comanda->linies_comanda()->updateExistingPivot($producte->id, ['quantitat' => $novaQuantitatTotal]);
            echo "Ja has comprat aquest producte. Nova quantitat: $novaQuantitatTotal";
        } else {
            // Si el producto no está en la comanda, agrégalo
            $comanda->linies_comanda()->attach($producte->id, ['quantitat' => $novaQuantitat]);
            echo "No has comprat aquest producte. Afegit a la comanda.";
        }
    
        echo "Has comprat $novaQuantitat unitat(s) de $producte->nom.";
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
