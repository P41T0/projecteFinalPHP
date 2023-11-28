<?php

namespace App\Http\Controllers;
use App\Models\Seccio;

use Illuminate\Http\Request;

class IniciController extends Controller
{
    //
    public function index() {
        $seccions = Seccio::all();

        return view('inici', compact("seccions"));
    }
}
