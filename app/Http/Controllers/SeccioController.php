<?php

namespace App\Http\Controllers;

use App\Models\Seccio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SeccioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $seccio = Seccio::all();

        return view('seccions.selSeccio', ['seccions' => $seccio]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('seccions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'nom' => 'required|string|max:100',
                'nomEs' => 'required|string|max:100',
                'nomEn' => 'required|string|max:100',
                'descripcio' => 'required|string|max:255',
                'descripcioEs' => 'required|string|max:255',
                'descripcioEn' => 'required|string|max:255',
            ],
            [
                'required' => 'El camp :attribute és obligatori',
                'size' => 'El camp :attribute no pot superar :max caràcters',
            ]
        );
        $seccio = new Seccio;
        $seccio->nom = $request->input('nom');
        $seccio->nom_es = $request->input('nomEs');
        $seccio->nom_en = $request->input('nomEn');
        $seccio->descripcio = $request->input('descripcio');
        $seccio->descripcio_es = $request->input('descripcioEs');
        $seccio->descripcio_en = $request->input('descripcioEn');
        $seccio->mostra_sec = false;
        $seccio->save();
        // dd($seccio);
        Session::flash('message', 'seccio modificada !');

        return redirect()->route('seccions.select');
    }

    /**
     * Display the specified resource.
     */
    public function show(Seccio $seccio) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Seccio $seccio)
    {
        return View(
            'seccions.edit',
            ['seccio' => $seccio]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Seccio $seccio)
    {
        //
        $request->validate(
            [
                'nom' => 'required|string|max:100',
                'nomEs' => 'required|string|max:100',
                'nomEn' => 'required|string|max:100',
                'descripcio' => 'required|string|max:255',
                'descripcioEs' => 'required|string|max:255',
                'descripcioEn' => 'required|string|max:255',
            ],
            [
                'required' => 'El camp :attribute és obligatori',
                'size' => 'El camp :attribute no pot superar :max caràcters',
            ]
        );

        $seccio->nom = $request->input('nom');
        $seccio->nom_es = $request->input('nomEs');
        $seccio->nom_en = $request->input('nomEn');
        $seccio->descripcio = $request->input('descripcio');
        $seccio->descripcio_es = $request->input('descripcioEs');
        $seccio->descripcio_en = $request->input('descripcioEn');
        $seccio->mostra_sec = $request->input('mostraSec') ? true : false;
        $seccio->save();
        // dd($seccio);
        Session::flash('message', 'seccio modificada !');

        return redirect()->route('seccions.select');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seccio $seccio)
    {
        //
    }
}
