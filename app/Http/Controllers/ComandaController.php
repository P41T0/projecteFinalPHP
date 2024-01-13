<?php

namespace App\Http\Controllers;

use App\Models\Comanda;
use App\Models\Producte;
use App\Models\Botiga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $comanda->botiga_id = 2;
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
        return view('compres.detall',['numProductes'=>$numProds,'comanda'=>$comanda, 'preuTotal'=>$preuTotal, 'usuari'=>$usuari,'botigues'=>$botiga]);
    }
    public function confirmar(Comanda $comanda)
    {
        $missatge = "";
        $preuTotal = 0;
        $botigues = Botiga::find($comanda->botiga_id);
        $botiga = $botigues->poblacio;
        $usuari = Auth()->user()->id;
        if ($comanda->usuari_id == $usuari){
        if ($comanda->oberta != 1){
            foreach ($comanda->productes as $prods) {
                $preuTotal += $prods->preu_unitari * $prods->pivot->quantitat;
            }
            $missatge = 0;
        }else if($comanda->productes->isEmpty()){
            $missatge = 1;
        }
        else {
            $comanda->oberta = 0;
            $comanda->save();
            
            
            foreach ($comanda->productes as $prods) {
                $preuTotal += $prods->preu_unitari * $prods->pivot->quantitat;
            }
            $missatge = 2;
        }
        } else {
            $missatge = 3;
        }

        return view('compres.confirma', compact('comanda', 'missatge','preuTotal','botiga'));
    }


    public function canviQuantitat(Request $request, Comanda $comanda)
    {
        if($comanda->oberta==1){
        $productes = $request->input('productes');
        if($productes != NULL){
        foreach ($productes as $producteId => $quantitat) {
            if ($quantitat <= 0) {
                $comanda->productes()->detach($producteId);
            }else{
                if ($quantitat==NULL){
                    $quantitat=1;
                }
                if ($quantitat>10){
                    $quantitat = 10;
                }
            $comanda->productes()->updateExistingPivot($producteId, ['quantitat' => $quantitat]);
            }
        }
        $comanda->botiga_id=$request->input('botiga');
        $comanda->save();
    }
        if ($comanda->productes->isEmpty()) {
            return redirect()->route('inici');
        }
        return redirect()->route('comprarNP');
    }else{
        return redirect()->route('confirma.compres',$comanda->id);
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
