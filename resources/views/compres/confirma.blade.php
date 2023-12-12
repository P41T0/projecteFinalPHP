<x-guest-layout>
    <div class="bg-verd3 rounded-md m-5 p-5">
    <h3 class="text-center font-bold text-xl">Compra confirmada</h3>
    <table class="">
        <tr><th>Nom Producte</th><th>Quantitat</th><th>Preu Unitari</th></tr>
        
    @forelse ($comanda->productes as $prod)
        <tr><td>{{$prod->nom}}</td><td class="text-right">{{$prod->pivot->quantitat}}</td><td class="text-right">{{$prod->preu_unitari}}€</td></tr>
    @empty
        <p>No hi ha productes comprats</p>
    @endforelse
    </table>

    @switch($missatge)
        @case(0)
        <p>{{_("La comanda ja s'ha tancat anteriorment, el preu total de la comanda és de")}} {{$preuTotal}} €</p>
            @break
        @case(1)
        <p>{{_("No hi ha cap producte en la comanda seleccionada")}}</p>
            @break
        @case(2)
        <p>{{_("Comanda tancada, el cost total és de")}}{{$preuTotal}}€</p>
        @break
        @case(3)
        <p>{{_("La comanda no es correspon amb l'usuari introduït")}}</p>
        @break
        @default
            <p>{{_("S'ha produit un error en el controlador")}}</p>
            @break
    @endswitch

    <a class="justify-center font-bold hover:bg-verd4 p-2 rounded"href="{{route('inici')}}">Tornar a l'inici</a>
    </div>
</x-guest-layout>