
    <x-guest-layout>
        <div class="bg-verd2 max-w-4xl ml-auto mr-auto mt-12 mb-12 rounded-lg p-5 justify-center text-center">
        <h2 class="text-3xl text-center font-extrabold">{{$producte->nom}}</h2>
        <p>{{$producte->descripcio}}</p>
        <img class="max-w-xl" src="https://www.p41t.com/storage/app/public/{{$producte->foto}}" alt="{{$producte->nom}}">
        <p>{{__("Preu")}}: {{$producte->preu_unitari}}€</p>
        @if($producte->mostra_prod && $producte->seccio->mostra_sec)
        <a class=" hover:bg-verd4 p-2 m-2 rounded-sm" href="{{route('comprar', $producte->id)}}">{{__("Afegir a la llista de la compra")}}</a>
        @else
        <p>{{__("Aquest producte no es pot afegir a cap comanda actualment")}}</p>
        @endif
        <p>{{__("El producte es troba disponible en les botigues de")}}:</p>
        <ul>
        @forelse ($producte->botiga as $botiga)
            @if($botiga->pivot->quantitat>0)
                <li>{{$botiga->poblacio}} --> {{$botiga->pivot->quantitat}}</li>
            @elseif($botiga->pivot->quantitat<=0)
                <li>{{__("El producte no es troba disponible a")}} {{$botiga->poblacio}}</li>
            @endif
        @empty
            <li>{{__("El producte no es troba disponible en cap botiga")}}</li>
        @endforelse
        </ul>
        </div>
    </x-guest-layout>
