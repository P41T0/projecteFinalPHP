<x-layouts::app>
    <div class="mx-auto my-12 max-w-4xl justify-center rounded-2xl border border-green-200 bg-white p-6 text-center shadow-sm sm:p-8 dark:border-zinc-800 dark:bg-zinc-900">
        @if (App::getLocale() == 'ca')
            <h2 class="text-3xl text-center font-extrabold">{{ $producte->nom }}</h2>
        @elseif(App::getLocale() == 'es')
            <h2 class="text-3xl text-center font-extrabold">{{ $producte->nom_es }}</h2>
        @elseif(App::getLocale() == 'en')
            <h2 class="text-3xl text-center font-extrabold">{{ $producte->nom_en }}</h2>
        @endif
        @if (App::getLocale() == 'ca')
            <p>{{ $producte->descripcio }}</p>
        @elseif(App::getLocale() == 'es')
            <p>{{ $producte->descripcio_es }}</p>
        @elseif(App::getLocale() == 'en')
            <p>{{ $producte->descripcio_en }}</p>
        @endif
        <div class="flex justify-center">
            <img class="max-w-xl" src="https://www.p41t.com/storage/app/public/{{ $producte->foto }}"
                alt="{{ $producte->nom }}">
        </div>

        <p>{{ __('Preu') }}: {{ $producte->preu_unitari }}€</p>
        @if ($producte->mostra_prod && $producte->seccio->mostra_sec)
            <p class="m-4"><a class="rounded-sm bg-green-700 p-2 text-center text-white hover:bg-green-800 dark:bg-green-400 dark:text-green-950 dark:hover:bg-green-300"
                href="{{ route('comprar', $producte->id) }}">{{ __('Afegir a la llista de la compra') }}</a></p>
        @else
            <p>{{ __('Aquest producte no es pot afegir a cap comanda actualment') }}</p>
        @endif
        <p>{{ __('El producte es troba disponible en les botigues de') }}:</p>
        <ul>
            @forelse ($producte->botiga as $botiga)
                @if ($botiga->pivot->quantitat > 0)
                    <li>{{ $botiga->poblacio }} --> {{ $botiga->pivot->quantitat }}{{__(" unitats disponibles")}}</li>
                @elseif($botiga->pivot->quantitat <= 0)
                    <li>{{ __('El producte no es troba disponible a') }} {{ $botiga->poblacio }}</li>
                @endif
            @empty
                <li>{{ __('El producte no es troba disponible en cap botiga') }}</li>
            @endforelse
        </ul>
    </div>
</x-layouts::app>
