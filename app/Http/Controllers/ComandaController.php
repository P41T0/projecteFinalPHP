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
        $liniaComanda = $comanda->productes()->where('producte_id', $producte->id)->first();

        $novaQuantitat = 1;
    
        if ($liniaComanda) {

            
        } else {

            $comanda->productes()->attach($producte->id, ['quantitat' => $novaQuantitat]);
        }
        $preuTotal=0;
        foreach($comanda->productes as $prods){
            $preuTotal+=$prods->preu_unitari * $prods->pivot->quantitat;
        }
        $usuari=Auth()->user()->id;
       return view('compres.detall',compact('comanda','preuTotal','usuari'));
    }
    public function confirmar(Comanda $comanda,$usuari){
        $preuTotal=0;
        if($comanda->usuari_id==$usuari){
        $comanda->oberta=0;
        $comanda->save();
        
        echo "usuari correcte";       
         foreach($comanda->productes as $prods){
            $preuTotal+=$prods->preu_unitari * $prods->pivot->quantitat;
        }
       
    } else{
            echo "usuari incorrecte";
        }

        return view('compres.confirma',compact('comanda','preuTotal'));
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
