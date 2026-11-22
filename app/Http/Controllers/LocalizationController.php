<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;

class LocalizationController extends Controller
{
    public function index($idioma)
    {
        // Apply the locale to the current request.
        App::setlocale($idioma);
        // Persist the locale so subsequent requests use it too.
        session()->put('idioma', $idioma);

        return redirect()->back();
    }
}
