<x-guest-layout>
    <div class="bg-verd2 max-w-4xl ml-auto mr-auto mt-12 mb-12 rounded-lg p-5">
        <h2 class="text-3xl text-center font-extrabold">{{ __('Edita la secció') }}</h2>


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
    <form action="{{ route('seccions.update', ['seccio' => $seccio->id]) }}" method="post" class="col-lg-6">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
        <label class="block py-2">
            <span class="text-gray-700">Nom:
                <input class=" w-full rounded-lg" type="text" name="nom" value="{{ $seccio->nom }}">
        </label>
        <label class="block py-2">
            <span class="text-gray-700">Nom (en castellà):
                <input class=" w-full rounded-lg" type="text" name="nomEs" value="{{ $seccio->nom_es }}">
        </label>
        <label class="block py-2">
            <span class="text-gray-700">Nom (en anglès):
                <input class=" w-full rounded-lg" type="text" name="nomEn" value="{{ $seccio->nom_en}}">
        </label>
        <label class="block py-2">
            <span class="text-gray-700">descripcio:
                <input class=" w-full rounded-lg" type="text" name="descripcio" value="{{ $seccio->descripcio}}">
        </label>
        <label class="block py-2">
            <span class="text-gray-700">descripcio (en castellà):
                <input class=" w-full rounded-lg" type="text" name="descripcioEs" value="{{ $seccio->descripcio_es}}">
        </label>
        <label class="block py-2">
            <span class="text-gray-700">descripcio (en anglès):
                <input class=" w-full rounded-lg" type="text" name="descripcioEn" value="{{ $seccio->descripcio_en}}">
        </label>
    </label>
    <label class="block py-2">
        <span class="text-gray-700">Mostrar secció:
            <input type="checkbox" name="mostraSec" {{$seccio->mostra_sec ? 'checked' : ''}} id="">
    </label>



        <br /><br />
        <x-primary-button class="ml-4 py-2">
            &radic; Guardar
        </x-primary-button>
        <a href="{{ route('seccions.select') }}"
            class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
            onclick="return confirm('{{ __('Segur que vols sortir?') }}')">
            &cross; Cancel·lar
        </a>
    </form>
    </div>
</x-guest-layout>