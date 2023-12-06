<x-guest-layout>
    <div class="bg-verd3 rounded-md m-5 p-5">
    <h3 class="text-center font-bold text-xl">productes comprats</h3>
    <form action="{{ route('actualiza.quantitat',[$comanda->id]) }}" method="POST">
        @csrf
        <table>
            <tr>
                <th>Nom Producte</th>
                <th>Quantitat</th>
                <th>Preu Unitari</th>
            </tr>
    
            @forelse ($comanda->productes as $prod)
                <tr>
                    <td>{{$prod->nom}}</td>
                    <td class="text-right">
                        <input type="number" name="productes[{{$prod->id}}]" value="{{ old('productos.'.$prod->id, $prod->pivot->quantitat) }}" min="0">
                        @error('productos.'.$prod->id)
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </td>
                    <td class="text-right">{{$prod->preu_unitari}}€</td>
                </tr>
            @empty
                <p>No hi ha productes comprats</p>
            @endforelse
        </table>
    
        <button type="submit">Guardar canvis</button>
    </form>
    <p>Preu total dels productes de la llista de la compra: {{$preuTotal}}€</p>
    <a class="justify-center font-bold hover:bg-verd4 p-2 rounded"href="{{route('inici')}}">Seguir comprant</a>
   
    <a class="justify-center font-bold hover:bg-verd4 p-2 rounded"  onclick="return confirm('Segur que vols confirmar la compra?')"href="{{route('confirma.compres', [$comanda->id,$usuari])}}">Confirma la comanda</a>
    </div>
</x-guest-layout>