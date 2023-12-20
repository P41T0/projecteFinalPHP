<x-guest-layout>
    <div class="bg-verd2 max-w-4xl ml-auto mr-auto mt-12 mb-12 rounded-lg p-5">
        <h2 class="text-3xl text-center font-extrabold">{{ __('editar') }}</h2>


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

        <form action="{{ route('productes.update', ['producte' => $producte->id]) }}" method="post" class="col-lg-6">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <label class="block py-2">
                <span class="text-gray-700">Nom:
                    <input class=" w-full rounded" type="text" name="nom" value="{{ $producte->nom }}">
            </label>
            <label class="block py-2">
                <span class="text-gray-700">Imatge
                    
                    <input type="file" accept="image/x-png,image/gif,image/jpeg" name="" id="">
            </label>
            <label class="block py-2">
                <span class="text-gray-700">Imatge
                    <input type="number" name="preu" step="0.01" min="0" id="">
                    
            </label>

            <label class="block py-2">
                <span class="text-gray-700">Seccio:
                    <select class="px-4 py-3 w-full rounded-full" name="seccio" >
                        @foreach ($seccions as $seccio)
                        <option value="{{$seccio->id}}" {{ $seccio->id == $producte->seccio_id ? 'selected' : '' }}>
                            {{$seccio->nom}}</option>
                        @endforeach
                    </select>

            </label>


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
