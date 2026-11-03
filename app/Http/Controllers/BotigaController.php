<?php

namespace App\Http\Controllers;

use App\Models\Botiga;
use App\Models\Producte;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BotigaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $botiga = Botiga::All();

        return view('botigues.selBotiga', ['botigues' => $botiga]);
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
        $validated = $request->validate([
            'productes' => ['required', 'array', 'min:1'],
            'productes.*' => ['required', 'integer', 'min:0'],
        ]);

        $producteIds = array_keys($validated['productes']);
        $attachedIds = $botiga->productes()
            ->whereIn('producte.id', $producteIds)
            ->pluck('producte.id')
            ->all();

        if (count($attachedIds) !== count($producteIds)) {
            throw ValidationException::withMessages([
                'productes' => 'Un o més productes no pertanyen a aquesta botiga.',
            ]);
        }

        foreach ($validated['productes'] as $producteId => $quantitat) {
            $botiga->productes()->updateExistingPivot($producteId, ['quantitat' => $quantitat]);
        }

        return redirect()->route('botigues.select');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Botiga $botiga)
    {
        //
    }
}
