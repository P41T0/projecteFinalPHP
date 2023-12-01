
    <x-guest-layout>
        <div class="bg-verd2 max-w-4xl ml-auto mr-auto mt-12 mb-12 rounded-lg p-5">
        <h2 class="text-3xl text-center font-extrabold">{{$producte->nom}}</h2>
        <p>{{$producte->descripcio}}</p>
        <img class="max-w-xl" src="{{$producte->foto}}" alt="{{$producte->nom}}">
        <p>Preu: {{$producte->preu_unitari}}</p>
        <a href="{{route('comprar', $producte->id)}}">Afegir a la llista de la compra</a>

        </div>
    </x-guest-layout>
