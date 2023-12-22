<?php

namespace App\Http\Controllers;

use App\Models\Producte;
use Illuminate\Http\Request;
use App\Models\Seccio;

use Illuminate\Support\Facades\Session;


class ProducteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $producte = Producte::all();
        return view('productes.selProducte',['productes'=>$producte]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("productes.create",['seccions' => Seccio::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate(
			$request,
			[
				 'nom' => 'required|max:100',
                 'descripcio' => 'required|max:1000',
                 'imatge' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                 'preu' => 'required',
                 'seccio' => 'required'
			],
			$messages = [
				'required'  => 'El camp :attribute és obligatori',
				'size'      => 'El camp :attribute no pot superar :max caràcters',
			]
		);
        $producte = new Producte;
        $producte->nom = $request->input('nom');
        $producte->descripcio = $request->input('descripcio');
        $producte->seccio_id = $request->input('seccio');
        if ($request->hasFile('imatge')) {
            
            $imatge = $request->file('imatge');

            $nomImg = time() . '_' . $imatge->getClientOriginalName();

           $request->file('imatge')->storeAs("/public/$nomImg");
           $producte->foto=$nomImg;

        }
        if ($request->input("preu")>0){
            $producte->preu_unitari = $request->input('preu');
        }
		$producte->save();
//dd($producte);
		Session::flash('message', 'producte modificat !');
		return redirect()->route("inici");
    }

    /**
     * Display the specified resource.
     */
    public function show(Producte $producte)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producte $producte)
    {
        //
        return View(
			'productes.edit',
			['producte' => $producte, 'seccions' => Seccio::all()]
		);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producte $producte)
    {
        //
        $this->validate(
			$request,
			[
				 'nom' => 'required|max:100',
                 'descripcio' => 'required|max:1000',
                 'imatge' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                 'preu' => 'required'
                // 'seccio' => 'required'
			],
			$messages = [
				'required'  => 'El camp :attribute és obligatori',
				'size'      => 'El camp :attribute no pot superar :max caràcters',
			]
		);

        $producte->nom = $request->input('nom');
        $producte->descripcio = $request->input('descripcio');
        $producte->seccio_id = $request->input('seccio');
        if ($request->hasFile('imatge')) {
            
            $imatge = $request->file('imatge');

            $nomImg = time() . '_' . $imatge->getClientOriginalName();

           $request->file('imatge')->storeAs("/public/$nomImg");
           $producte->foto=$nomImg;

        }
        if ($request->input("preu")>0){
            $producte->preu_unitari = $request->input('preu');
        }
		$producte->save();
//dd($producte);
		Session::flash('message', 'producte modificat !');
		return redirect()->route("inici");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producte $producte)
    {
        //
    }
}
