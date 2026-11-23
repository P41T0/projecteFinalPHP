<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;

class LocalizationController extends Controller
{
    public function index(string $idioma): RedirectResponse
    {
        // Apply the locale to the current request.
        App::setlocale($idioma);
        // Persist the locale so subsequent requests use it too.
        session()->put('idioma', $idioma);

        return redirect()->back();
    }
}
