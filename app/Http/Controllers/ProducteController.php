<?php

namespace App\Http\Controllers;

use App\Models\Botiga;
use App\Models\Producte;
use App\Models\Seccio;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use RuntimeException;

class ProducteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $producte = Producte::all();

        return view('productes.selProducte', ['productes' => $producte]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorizeAdmin();

        return view('productes.create', ['seccions' => Seccio::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        /** @var array{nom: string, nomEs: string, nomEn: string, descripcio: string, descripcioEs: string, descripcioEn: string, imatge?: UploadedFile|null, preu: int|float|string, seccio: string} $validated */
        $validated = $request->validate(
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
        $producte->nom = $validated['nom'];
        $producte->nom_es = $validated['nomEs'];
        $producte->nom_en = $validated['nomEn'];
        $producte->descripcio = $validated['descripcio'];
        $producte->descripcio_es = $validated['descripcioEs'];
        $producte->descripcio_en = $validated['descripcioEn'];
        $producte->seccio_id = $validated['seccio'];
        $image = $validated['imatge'] ?? null;
        if ($image instanceof UploadedFile) {
            $producte->foto = $this->storeImage($image);
        }
        $producte->preu_unitari = (float) $validated['preu'];
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
    public function show(Producte $producte): void {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producte $producte): View
    {
        $this->authorizeAdmin();

        return View(
            'productes.edit',
            ['producte' => $producte, 'seccions' => Seccio::all()]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producte $producte): RedirectResponse
    {
        $this->authorizeAdmin();

        /** @var array{nom: string, nomEs: string, nomEn: string, descripcio: string, descripcioEs: string, descripcioEn: string, imatge?: UploadedFile|null, preu: int|float|string, seccio: string} $validated */
        $validated = $request->validate(
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

        $producte->nom = $validated['nom'];
        $producte->nom_es = $validated['nomEs'];
        $producte->nom_en = $validated['nomEn'];
        $producte->descripcio = $validated['descripcio'];
        $producte->descripcio_es = $validated['descripcioEs'];
        $producte->descripcio_en = $validated['descripcioEn'];
        $producte->seccio_id = $validated['seccio'];
        $image = $validated['imatge'] ?? null;
        if ($image instanceof UploadedFile) {
            $producte->foto = $this->storeImage($image);
        }
        $producte->preu_unitari = (float) $validated['preu'];
        $producte->mostra_prod = $request->boolean('mostraProd');
        $producte->save();
        Session::flash('message', 'producte modificat !');

        return redirect()->route('productes.select');
    }

    /**
     * @return array<int, string|Closure>
     */
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
                    $fail((string) __('validation.image'));
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
    public function destroy(Producte $producte): void
    {
        $this->authorizeAdmin();
    }
}
