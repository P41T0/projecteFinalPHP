<?php

namespace App\Http\Controllers;

use App\Models\Producte;
use App\Models\Seccio;

class IniciController extends Controller
{
    //
    public function index()
    {
        $seccions = Seccio::all();

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
