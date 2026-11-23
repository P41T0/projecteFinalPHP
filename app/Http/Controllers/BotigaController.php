<?php

namespace App\Http\Controllers;

use App\Models\Botiga;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BotigaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $botiga = Botiga::All();

        return view('botigues.selBotiga', ['botigues' => $botiga]);
    }

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
    public function show(Botiga $botiga): void {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Botiga $botiga): View
    {
        $this->authorizeAdmin();

        return View(
            'botigues.edit',

            ['botiga' => $botiga]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Botiga $botiga): RedirectResponse
    {
        $this->authorizeAdmin();

        /** @var array{productes: array<array-key, int|string>} $validated */
        $validated = $request->validate([
            'productes' => ['required', 'array', 'min:1'],
            'productes.*' => ['required', 'integer', 'min:0'],
        ]);
        $productes = $validated['productes'];

        $producteIds = array_keys($productes);
        $attachedIds = $botiga->productes()
            ->whereIn('producte.id', $producteIds)
            ->pluck('producte.id')
            ->all();

        // Inventory can only be changed for products already associated with this store.
        if (count($attachedIds) !== count($productes)) {
            throw ValidationException::withMessages([
                'productes' => 'Un o més productes no pertanyen a aquesta botiga.',
            ]);
        }

        foreach ($productes as $producteId => $quantitat) {
            $botiga->productes()->updateExistingPivot($producteId, ['quantitat' => (int) $quantitat]);
        }

        return redirect()->route('botigues.select');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Botiga $botiga): void
    {
        $this->authorizeAdmin();
    }
}
