<x-guest-layout>
    <div class="bg-verd3 rounded-md m-5 p-5">
    <h3 class="text-center font-bold text-xl">{{__("Compra confirmada")}}</h3>
    <table class="">
        <tr><th>{{__("Nom del producte")}}</th><th>{{__("Quantitat")}}</th><th>{{__("Preu unitari")}}</th></tr>
        
    @forelse ($comanda->productes as $prod)
        <tr><td>{{$prod->nom}}</td><td class="text-right">{{$prod->pivot->quantitat}}</td><td class="text-right">{{$prod->preu_unitari}}€</td></tr>
    @empty
        <p>No hi ha productes comprats</p>
    @endforelse
    </table>

    @switch($missatge)
        @case(0)
        <p>{{__("La comanda ja s'ha tancat anteriorment, el preu total de la comanda és de")}} {{$preuTotal}} €</p>
            @break
        @case(1)
        <p>{{__("No hi ha cap producte en la comanda seleccionada")}}</p>
            @break
        @case(2)
        <p>{{__("Comanda tancada, el cost total és de")}}{{$preuTotal}}€</p>
        @break
        @case(3)
        <p>{{__("La comanda no es correspon amb l'usuari introduït")}}</p>
        @break
        @default
            <p>{{__("S'ha produit un error en el controlador")}}</p>
            @break
    @endswitch

    <a class="justify-center font-bold hover:bg-verd4 p-2 rounded"href="{{route('inici')}}">{{__("Tornar a l'inici")}}</a>
    </div>
</x-guest-layout>