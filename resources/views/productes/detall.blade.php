
    <x-guest-layout>
        <div class="bg-verd2 max-w-4xl ml-auto mr-auto mt-12 mb-12 rounded-lg p-5">
        <h2 class="text-3xl text-center font-extrabold">{{$producte->nom}}</h2>
        <p>{{$producte->descripcio}}</p>
        <img class="max-w-xl" src="{{asset("/storage/$producte->foto")}}" alt="{{$producte->nom}}">
        <p>{{__("Preu")}}: {{$producte->preu_unitari}}€</p>
        <a href="{{route('comprar', $producte->id)}}">{{__("Afegir a la llista de la compra")}}</a>
        <p>{{__("El producte es troba disponible en les botigues de")}}:</p>
        <ul>
        @forelse ($producte->botiga as $botiga)
            @if($botiga->pivot->quantitat>0)
                <li>{{$botiga->poblacio}} --> {{$botiga->pivot->quantitat}}</li>
            @elseif($botiga->pivot->quantitat==0)
                <li>{{__("El produte no es troba disponible temporalment a")}} {{$botiga->poblacio}}</li>
            @endif
        @empty
            <li>{{__("El producte no es troba disponible en cap botiga")}}</li>
        @endforelse
        </ul>
        </div>
    </x-guest-layout>
