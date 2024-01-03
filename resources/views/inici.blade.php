<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inici') }}
        </h2>
    </x-slot>

    <div class="w-fit mx-auto sm:px-6 lg:px-8">
        <div class="p-6 text-gray-900">
            @forelse ($seccions as $seccio)
                @if ($seccio->mostra_sec)
                    <div class=" bg-verd1 m-2 p-2 rounded-md">
                        @if (App::getLocale() == 'ca')
                            <h2 class="text-center font-bold text-2xl">{{ $seccio->nom }}</h2>
                        @elseif(App::getLocale() == 'es')
                            <h2 class="text-center font-bold text-2xl">{{ $seccio->nom_es }}</h2>
                        @elseif(App::getLocale() == 'en')
                            <h2 class="text-center font-bold text-2xl">{{ $seccio->nom_en }}</h2>
                        @endif

                        @if (App::getLocale() == 'ca')
                            <caption>{{ $seccio->descripcio }}</caption>
                        @elseif(App::getLocale() == 'es')
                            <caption>{{ $seccio->descripcio_es }}</caption>
                        @elseif(App::getLocale() == 'en')
                            <caption>{{ $seccio->descripcio_en }}</caption>
                        @endif
                        <div class="flex flex-wrap text-center max-w-64">
                            @foreach ($seccio->productes as $producte)
                                @if ($producte->mostra_prod)
                                    <div class=" bg-verd3 m-2 max-w-72 p-2 rounded-md">
                                        @if (App::getLocale() == 'ca')
                                            <p class="font-semibold text-lg">{{ $producte->nom }}</p>
                                        @elseif(App::getLocale() == 'es')
                                            <p class="font-semibold text-lg">{{ $producte->nom_es }}</p>
                                        @elseif(App::getLocale() == 'en')
                                            <p class="font-semibold text-lg">{{ $producte->nom_en }}</p>
                                        @endif

                                        <img src="https://www.p41t.com/storage/app/public/{{ $producte->foto }}"
                                            class="m-auto max-w-56 max-h-56" alt="imatge d'un element">
                                        <p>{{ $producte->preu_unitari }}€</p>
                                        <a class="hover:bg-verd4 p-2 m-4 rounded-sm text-center"
                                            href="{{ route('detall.producte', $producte->id) }}">{{ __('Comprar') }}</a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @empty
                {{ __('No hi ha seccions') }}
            @endforelse
        </div>
    </div>
    </div>
</x-guest-layout>
