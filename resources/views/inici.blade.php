<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inici') }}
        </h2>
    </x-slot>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 text-gray-900">
                    @forelse ($seccions as $seccio)
                        <div class="max-w-2xl bg-verd1 m-2 p-2 rounded-md">
                        <h2 class="text-center font-bold text-2xl">{{$seccio->nom}}</h2>
                        <caption>{{$seccio->descripcio}}</caption>
                        <div class="flex text-center">
                        @foreach ($seccio->productes as $producte)
                            <div class="bg-verd3 m-2 p-2 rounded-md">
                            <p class="font-semibold text-lg">{{$producte->nom}}</p>
                            <img src="{{$producte->foto}}" alt="">
                            <p>{{$producte->preu_unitari,}}€</p>
                            <a href="{{route('comprar')}}">comprar</a>
                            </div>
                        @endforeach
                        </div>
                        </div>
                    @empty
                        {{__('No hi ha seccions')}}
                    @endforelse
                </div>
            </div>
    </div>
</x-guest-layout>
