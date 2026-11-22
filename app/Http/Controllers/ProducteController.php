<?php

namespace App\Http\Controllers;

use App\Models\Botiga;
use App\Models\Producte;
use App\Models\Seccio;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use RuntimeException;

class ProducteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $producte = Producte::all();

        return view('productes.selProducte', ['productes' => $producte]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('productes.create', ['seccions' => Seccio::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'nom' => 'required|string|max:100',
                'nomEs' => 'required|string|max:100',
                'nomEn' => 'required|string|max:100',
                'descripcio' => 'required|string|max:255',
                'descripcioEs' => 'required|string|max:255',
                'descripcioEn' => 'required|string|max:255',
                'imatge' => $this->imageRules(),
                'preu' => 'required|numeric|gte:0',
                'seccio' => 'required|uuid|exists:seccio,id',
            ],
            [
                'required' => 'El camp :attribute és obligatori',
                'size' => 'El camp :attribute no pot superar :max caràcters',
            ]
        );
        $producte = new Producte;
        $producte->nom = $request->input('nom');
        $producte->nom_es = $request->input('nomEs');
        $producte->nom_en = $request->input('nomEn');
        $producte->descripcio = $request->input('descripcio');
        $producte->descripcio_es = $request->input('descripcioEs');
        $producte->descripcio_en = $request->input('descripcioEn');
        $producte->seccio_id = $request->input('seccio');
        if ($request->hasFile('imatge')) {
            $producte->foto = $this->storeImage($request->file('imatge'));
        }
        $producte->preu_unitari = $request->input('preu');
        $producte->mostra_prod = false;
        $producte->save();
        // Initialize stock to zero in every store before the product becomes visible.
        $botigues = Botiga::all();
        foreach ($botigues as $botiga) {
            $producte->botiga()->attach($botiga->id, ['quantitat' => 0]);
        }

        $producte->save();

        Session::flash('message', 'producte modificat !');

        return redirect()->route('productes.select');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producte $producte)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producte $producte)
    {
        return View(
            'productes.edit',
            ['producte' => $producte, 'seccions' => Seccio::all()]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producte $producte)
    {
        $request->validate(
            [
                'nom' => 'required|string|max:100',
                'nomEs' => 'required|string|max:100',
                'nomEn' => 'required|string|max:100',
                'descripcio' => 'required|string|max:255',
                'descripcioEs' => 'required|string|max:255',
                'descripcioEn' => 'required|string|max:255',
                'imatge' => $this->imageRules(),
                'preu' => 'required|numeric|gte:0',
                'seccio' => 'required|uuid|exists:seccio,id',
            ],
            [
                'required' => 'El camp :attribute és obligatori',
                'size' => 'El camp :attribute no pot superar :max caràcters',
            ]
        );

        $producte->nom = $request->input('nom');
        $producte->nom_es = $request->input('nomEs');
        $producte->nom_en = $request->input('nomEn');
        $producte->descripcio = $request->input('descripcio');
        $producte->descripcio_es = $request->input('descripcioEs');
        $producte->descripcio_en = $request->input('descripcioEn');
        $producte->seccio_id = $request->input('seccio');
        if ($request->hasFile('imatge')) {
            $producte->foto = $this->storeImage($request->file('imatge'));
        }
        $producte->preu_unitari = $request->input('preu');
        $producte->mostra_prod = $request->input('mostraProd') ? true : false;
        $producte->save();
        Session::flash('message', 'producte modificat !');

        return redirect()->route('productes.select');
    }

    private function imageRules(): array
    {
        return [
            'bail',
            'nullable',
            'image',
            'mimes:jpeg,png,jpg,gif,webp',
            'max:2048',
            'dimensions:min_width=1,min_height=1,max_width=4096,max_height=4096',
            function (string $attribute, UploadedFile $file, Closure $fail): void {
                $contents = file_get_contents($file->getRealPath());

                if ($contents === false) {
                    throw new RuntimeException('Unable to read the product image.');
                }

                $image = @imagecreatefromstring($contents);
                if ($image === false) {
                    $fail('validation.image')->translate();
                }
            },
        ];
    }

    private function storeImage(UploadedFile $image): string
    {
        $path = $image->store('', 'public');

        if ($path === false) {
            throw new RuntimeException('Unable to store the product image.');
        }

        return $path;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producte $producte) {}
}
