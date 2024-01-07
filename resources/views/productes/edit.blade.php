<x-guest-layout>
    <div class="bg-verd2 max-w-4xl ml-auto mr-auto mt-12 mb-12 rounded-lg p-5">
        <h2 class="text-3xl text-center font-extrabold">{{ __('Edita el producte') }}</h2>


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

        <form action="{{ route('productes.update', ['producte' => $producte->id]) }}" method="post" class="col-lg-6" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <label class="block py-2">
                <span class="text-gray-700">Nom:
                    <input class=" w-full rounded-lg" type="text" name="nom" value="{{ $producte->nom }}">
            </label>
            <label class="block py-2">
                <span class="text-gray-700">Nom (en castellà):
                    <input class=" w-full rounded-lg" type="text" name="nomEs" value="{{ $producte->nom_es }}">
            </label>
            <label class="block py-2">
                <span class="text-gray-700">Nom (en anglès):
                    <input class=" w-full rounded-lg" type="text" name="nomEn" value="{{ $producte->nom_en }}">
            </label>
            <label class="block py-2">
                <span class="text-gray-700">Descripcio Producte:
                    <input class=" w-full rounded-lg" type="text" name="descripcio" value="{{ $producte->descripcio }}">
            </label>
            <label class="block py-2">
                <span class="text-gray-700">Descripcio Producte (en castellà):
                    <input class=" w-full rounded-lg" type="text" name="descripcioEs" value="{{ $producte->descripcio_es }}">
            </label>
            <label class="block py-2">
                <span class="text-gray-700">Descripcio Producte (en anglès):
                    <input class=" w-full rounded-lg" type="text" name="descripcioEn" value="{{ $producte->descripcio_en }}">
            </label>
            <label class="block py-2">
                <span class="text-gray-700">Imatge
                    
                    <input type="file" accept="image/x-png,image/gif,image/jpeg" name="imatge" id="" data-defaultvalue="{{asset("/storage/$producte->foto")}}">
            </label>
            <label class="block py-2">
                <span class="text-gray-700">Preu
                    <input class=" rounded-lg" type="number" name="preu" step="0.01" min="0" id="" value={{$producte->preu_unitari}}>
                    
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
            <label class="block py-2">
                <span class="text-gray-700">Mostrar producte:
                    <input type="checkbox" name="mostraProd" {{$producte->mostra_prod ? 'checked' : ''}} id="">
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
