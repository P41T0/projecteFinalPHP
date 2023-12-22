<?php

namespace App\Http\Controllers;

use App\Models\Botiga;
use Illuminate\Http\Request;
use App\Models\Producte;

class BotigaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $botiga = Botiga::All();
        return view('botigues.selBotiga',['botigues'=>$botiga]);
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
    public function show(Botiga $botiga)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Botiga $botiga)
    {
        return View(
			'botigues.edit',
            
			['botiga' => $botiga, 'productes' => Producte::all()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Botiga $botiga)
    {
        $productes = $request->input('productes');
        if($productes != NULL){
        foreach ($productes as $producteId => $quantitat) {

            if($quantitat>0){
                $quantitat=0;
            }
            if ($quantitat==NULL){
                    $quantitat=1;
            }
            $botiga->productes()->updateExistingPivot($producteId, ['quantitat' => $quantitat]);
            
    }}
return back();
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Botiga $botiga)
    {
        //
    }
}
