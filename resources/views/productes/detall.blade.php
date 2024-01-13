<x-guest-layout>
    <div class="bg-verd2 max-w-4xl ml-auto mr-auto mt-12 mb-12 rounded-lg p-5 justify-center text-center">
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
            <p class="m-4"><a class=" bg-verd4 hover:bg-verd5 p-2 rounded-sm text-center"
                href="{{ route('comprar', $producte->id) }}">{{ __('Afegir a la llista de la compra') }}</a></p>
        @else
            <p>{{ __('Aquest producte no es pot afegir a cap comanda actualment') }}</p>
        @endif
        <p>{{ __('El producte es troba disponible en les botigues de') }}:</p>
        <ul>
            @forelse ($producte->botiga as $botiga)
                @if ($botiga->pivot->quantitat > 0)
                    <li>{{ $botiga->poblacio }} --> {{ $botiga->pivot->quantitat }}</li>
                @elseif($botiga->pivot->quantitat <= 0)
                    <li>{{ __('El producte no es troba disponible a') }} {{ $botiga->poblacio }}</li>
                @endif
            @empty
                <li>{{ __('El producte no es troba disponible en cap botiga') }}</li>
            @endforelse
        </ul>
    </div>
</x-guest-layout>
