<x-layouts::app>
    <div class="border-b border-green-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
    <ul class="flex flex-wrap p-2 text-top-center justify-center">
        <li class="ml-5 mr-5 rounded-md p-1 hover:bg-green-100 dark:hover:bg-zinc-800"><a href="{{route('seccions.select')}}">Modificar seccions</a></li>
        <li class="ml-5 mr-5 rounded-md p-1 hover:bg-green-100 dark:hover:bg-zinc-800"><a href="{{route('productes.select')}}">Modificar productes</a></li>
    </ul>
</div>
    <h1 class="text-center text-xl font-bold">{{ __('Modificar botigues') }}</h1>
@foreach ($botigues as $botiga)
    <div class="m-2 rounded-xl border border-green-200 bg-white p-3 hover:bg-green-50 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:bg-zinc-800">
        <a href="{{route('botigues.edit', $botiga->id)}}">
        <p>{{$botiga->poblacio}}</p>
        <p>{{$botiga->adreca}}</p>
        </a>
    </div>
@endforeach
</x-layouts::app>