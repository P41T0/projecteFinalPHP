<?php

namespace App\Http\Controllers;

use App\Models\Seccio;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SeccioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $seccio = Seccio::all();

        return view('seccions.selSeccio', ['seccions' => $seccio]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorizeAdmin();

        return view('seccions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        /** @var array{nom: string, nomEs: string, nomEn: string, descripcio: string, descripcioEs: string, descripcioEn: string} $validated */
        $validated = $request->validate(
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
        $seccio->nom = $validated['nom'];
        $seccio->nom_es = $validated['nomEs'];
        $seccio->nom_en = $validated['nomEn'];
        $seccio->descripcio = $validated['descripcio'];
        $seccio->descripcio_es = $validated['descripcioEs'];
        $seccio->descripcio_en = $validated['descripcioEn'];
        $seccio->mostra_sec = false;
        $seccio->save();
        Session::flash('message', 'seccio modificada !');

        return redirect()->route('seccions.select');
    }

    /**
     * Display the specified resource.
     */
    public function show(Seccio $seccio): void {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Seccio $seccio): View
    {
        $this->authorizeAdmin();

        return View(
            'seccions.edit',
            ['seccio' => $seccio]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Seccio $seccio): RedirectResponse
    {
        $this->authorizeAdmin();

        /** @var array{nom: string, nomEs: string, nomEn: string, descripcio: string, descripcioEs: string, descripcioEn: string} $validated */
        $validated = $request->validate(
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

        $seccio->nom = $validated['nom'];
        $seccio->nom_es = $validated['nomEs'];
        $seccio->nom_en = $validated['nomEn'];
        $seccio->descripcio = $validated['descripcio'];
        $seccio->descripcio_es = $validated['descripcioEs'];
        $seccio->descripcio_en = $validated['descripcioEn'];
        $seccio->mostra_sec = $request->boolean('mostraSec');
        $seccio->save();
        Session::flash('message', 'seccio modificada !');

        return redirect()->route('seccions.select');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seccio $seccio): void
    {
        $this->authorizeAdmin();
    }
}
