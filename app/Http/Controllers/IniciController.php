<?php

namespace App\Http\Controllers;
use App\Models\Seccio;
use App\Models\Producte;

use Illuminate\Http\Request;

class IniciController extends Controller
{
    //
    public function index() {
        $seccions = Seccio::all();

        return view('inici', compact("seccions"));
    }
    public function showProducte(Producte $producte){
        return view('productes.detall', compact('producte'));
    }
}
