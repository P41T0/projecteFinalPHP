<x-guest-layout>
    <div class="bg-verd2 max-w-4xl ml-auto mr-auto mt-12 mb-12 rounded-lg p-5">
        <h2 class="text-3xl text-center font-extrabold">{{ __('Productes disponibles en la botiga de ') }}{{$botiga->poblacio}}</h2>


        @if (Session::has('message'))
            <div class="border-slate-200" onclick="this.style.display='none'">{{ Session::get('message') }}</div>
        @endif
        @if ($errors->any() > 0)
            <div class="text-red">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('botigues.update', ['botiga' => $botiga->id]) }}" method="post" class="col-lg-6">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            @forelse ($botiga->productes as $prod)
            <label class="block py-2">
              <span class="text-gray-700">{{$prod->nom}}
                    <input type="number" name="productes[{{$prod->id}}]" min="0" value="{{$prod->pivot->quantitat}}" id="">
              </span>
            </label>
            @empty
             <span>No hi ha cap producte a la venta en aquesta botiga</span>
          @endforelse

            


            <br /><br />
            <x-primary-button class="ml-4 py-2">
                &radic; Guardar
            </x-primary-button>
            <a href="{{ route('inici') }}"
                class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                onclick="return confirm('{{ __('Segur que vols sortir?') }}')">
                &cross; Cancel·lar
            </a>
        </form>
    </div>
</x-guest-layout>
