<x-layouts::app>
    <div class="justify-center border-b border-green-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
    <ul class="flex flex-wrap p-2 text-top-center justify-center">
        <li class="ml-5 mr-5 rounded-md p-1 hover:bg-green-100 dark:hover:bg-zinc-800"><a href="{{route('productes.select')}}">Modificar productes</a></li>
        <li class="ml-5 mr-5 rounded-md p-1 hover:bg-green-100 dark:hover:bg-zinc-800"><a href="{{route('botigues.select')}}">Modificar botigues</a></li>
    </ul>
</div>
    <h1 class="text-center text-xl font-bold">{{ __('Modificar seccions') }}</h1>
@foreach ($seccions as $seccio)
    <div class="m-2 rounded-xl border border-green-200 bg-white p-3 hover:bg-green-50 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:bg-zinc-800">
        <a href="{{route('seccions.edit', $seccio->id)}}">
        <p>{{$seccio->nom}} {{$seccio->mostra_sec?" (s'està mostrant actualment)":" (no s'està mostrant actualment)"}}</p>
        <p>{{$seccio->descripcio}}</p>
        </a>
    </div>
@endforeach
<a href="{{route('seccions.create')}}"><h1 class="m-2 rounded-xl bg-green-700 p-3 text-center font-medium text-white hover:bg-green-800 dark:bg-green-400 dark:text-green-950 dark:hover:bg-green-300">Crear una secció nova</h1></a>
</x-layouts::app>