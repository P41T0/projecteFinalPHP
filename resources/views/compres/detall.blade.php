<x-guest-layout>
    <div class="bg-verd3 rounded-md m-5 p-5">
        <h3 class="text-center font-bold text-xl">{{ __('Productes comprats') }}</h3>
        @if($numProductes<=0)
        <caption> {{__('No tens cap producte introduit en la llista de la compra en aquests moments.')}} <a class="hover:bg-verd4 p-2 rounded" href="{{Route("inici")}}">{{__('Prem aquí per a tornar a la pàgina inicial')}}</a></caption>
    </div>
        @else
        <form action="{{ route('actualiza.quantitat', [$comanda->id]) }}" method="POST">
            @csrf

            <caption>
                {{ __("Selecciona la quantitat del producte que vols comprar entre 1 i 10 i guarda els canvis. Si selecciones 0, el producte s'eliminarà de la comanda.") }}

            </caption>
            @if($numProductes==1)
            <caption>{{__('Tens ')}} {{$numProductes}}{{__(' producte afegit a aquesta comanda')}}</caption>
            @else 
            <caption>{{__('Tens ')}} {{$numProductes}}{{__(' productes afegits a aquesta comanda')}}</caption>
            @endif
            <table class=" border-verd5 border-solid border-5" border-style="solid">
                <tr>
                    <th>{{ __('Nom del producte') }}</th>
                    <th>{{ __('Quantitat') }}</th>
                    <th>{{ __('Preu unitari') }}</th>
                </tr>

                @forelse ($comanda->productes as $prod)
                    <tr>
                        @if (App::getLocale() == 'ca')
                            <td>{{ $prod->nom }}</td>
                        @elseif(App::getLocale() == 'es')
                            <td>{{ $prod->nom_es }}</td>
                        @elseif(App::getLocale() == 'en')
                            <td>{{ $prod->nom_en }}</td>
                        @endif

                        <td class="text-right">
                            <input class="rounded-lg m-2 border-none text-right" type="number"
                                name="productes[{{ $prod->id }}]"
                                value="{{ old('productos.' . $prod->id, $prod->pivot->quantitat) }}" min="0"
                                max="10" required>
                            @error('productos.' . $prod->id)
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </td>
                        <td class="text-right">{{ $prod->preu_unitari }}€</td>
                    </tr>
                @empty
                    <p>{{ __('No hi ha cap producte comprat en la comanda actual') }}</p>
                @endforelse
            </table>
            <label class="block py-2">
                <p>{{ __('Selecciona la botiga on recolliràs la comanda') }}</p>
                <select class=" rounded-lg" name="botiga" id="" class="px-4 py-3 rounded-full">
                    @foreach ($botigues as $botiga)
                        <option value="{{ $botiga->id }}"
                            {{ $botiga->id == $comanda->botiga_id ? 'selected' : '' }}>{{ $botiga->poblacio }}
                        </option>
                    @endforeach
                </select>
            </label>
            <button class="hover:bg-verd4 p-2 rounded" type="submit">{{ __('Guardar canvis') }}</button>
        </form>
        <p>{{ __('Preu total dels productes de la llista de la compra:') }} {{ $preuTotal }}€</p>
        <a
            class="justify-center font-bold hover:bg-verd4 p-2 rounded"href="{{ route('inici') }}">{{ __('Seguir comprant') }}</a>

        <a class="justify-center font-bold hover:bg-verd4 p-2 rounded"
            onclick="return confirm('{{ __('Estàs segur que vols confirmar la compra?') }}')"href="{{ route('confirma.compres', $comanda->id) }}">{{ __('Confirma la comanda') }}</a>
    </div>
    @endif
</x-guest-layout>
