<?php

namespace App\Http\Controllers;

use App\Models\Producte;
use App\Models\Seccio;

class IniciController extends Controller
{
    public function index()
    {
        $seccions = Seccio::all();
        // Load visible products in the same query for each visible section.
        $seccions->where('mostra_sec', true)->load([
            'productes' => fn ($query) => $query->where('mostra_prod', true),
        ]);

        return view('inici', compact('seccions'));
    }

    public function showProducte(Producte $producte)
    {
        return view('productes.detall', compact('producte'));
    }

    public function contacte()
    {
        return view('contacte');
    }
}
