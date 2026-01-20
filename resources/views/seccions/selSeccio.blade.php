<x-layouts::app>
    <div class="bg-green-100 justify-center">
    <ul class="flex flex-wrap p-2 text-top-center justify-center">
        <li class="hover:bg-green-300 rounded-md ml-5 mr-5 p-1"><a href="{{route('productes.select')}}">Modificar productes</a></li>
        <li class="hover:bg-green-300 rounded-md ml-5 mr-5 p-1"><a href="{{route('botigues.select')}}">Modificar botigues</a></li>
    </ul>
</div>
    <h1 class="text-xl font-bold text-center">Modificar seccions</h1>
@foreach ($seccions as $seccio)
    <div class="bg-green-200 hover:bg-green-400 p-2 m-2 rounded-sm">
        <a href="{{route('seccions.edit', $seccio->id)}}">
        <p>{{$seccio->nom}} {{$seccio->mostra_sec?" (s'està mostrant actualment)":" (no s'està mostrant actualment)"}}</p>
        <p>{{$seccio->descripcio}}</p>
        </a>
    </div>
@endforeach
<a href="{{route('seccions.create')}}"><h1 class="bg-green-200 hover:bg-green-400 p-2 m-2 rounded-sm text-center">Crear una secció nova</h1></a>
</x-layouts::app>