<?php

namespace App\Http\Controllers;

use App\Models\Producte;
use App\Models\Seccio;
use Illuminate\Contracts\View\View;

class IniciController extends Controller
{
    public function index(): View
    {
        $seccions = Seccio::all();
        // Load visible products in the same query for each visible section.
        $seccions->where('mostra_sec', true)->load([
            'productes' => fn ($query) => $query->where('mostra_prod', true),
        ]);

        return view('inici', compact('seccions'));
    }

    public function showProducte(Producte $producte): View
    {
        return view('productes.detall', compact('producte'));
    }

    public function contacte(): View
    {
        return view('contacte');
    }
}
