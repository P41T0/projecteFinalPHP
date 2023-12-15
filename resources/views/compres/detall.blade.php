<x-guest-layout>
    <div class="bg-verd3 rounded-md m-5 p-5">
    <h3 class="text-center font-bold text-xl">{{__("Productes comprats")}}</h3>
    <form action="{{ route('actualiza.quantitat',[$comanda->id]) }}" method="POST">
        @csrf
        <caption>{{__("Selecciona la quantitat del producte que vols comprar entre 1 i 10 i guarda els canvis. Si selecciones 0, el producte s'eliminarà de la comanda.")}}</caption>
        <table class=" border-verd5 border-solid border-5" border-style="solid">
            <tr>
                <th>{{__("Nom del producte")}}</th>
                <th>{{__("Quantitat")}}</th>
                <th>{{__("Preu unitari")}}</th>
            </tr>
    
            @forelse ($comanda->productes as $prod)
                <tr>
                    <td>{{$prod->nom}}</td>
                    <td class="text-right">
                        <input class="bg-verd4 rounded-lg m-2 border-none text-right" type="number" name="productes[{{$prod->id}}]" value="{{ old('productos.'.$prod->id, $prod->pivot->quantitat) }}" min="0" max="10" required>
                        @error('productos.'.$prod->id)
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </td>
                    <td class="text-right">{{$prod->preu_unitari}}€</td>
                </tr>
            @empty
                <p>{{__("No hi ha cap producte comprat en la comanda actual")}}</p>
            @endforelse
        </table>
    
        <button type="submit">{{__("Guardar canvis")}}</button>
    </form>
    <p>{{__("Preu total dels productes de la llista de la compra:")}} {{$preuTotal}}€</p>
    <a class="justify-center font-bold hover:bg-verd4 p-2 rounded"href="{{route('inici')}}">{{__("Seguir comprant")}}</a>
   
    <a class="justify-center font-bold hover:bg-verd4 p-2 rounded"  onclick="return confirm('{{__('Estàs segur que vols confirmar la compra?')}}')"href="{{route('confirma.compres', $comanda->id)}}">{{__("Confirma la comanda")}}</a>
    </div>
</x-guest-layout>