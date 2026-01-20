<x-layouts::app>
    <div class="bg-green-100">
    <ul class="flex flex-wrap p-2 text-top-center justify-center">
        <li class="hover:bg-green-300 rounded-md ml-5 mr-5 p-1"><a href="{{route('seccions.select')}}">Modificar seccions</a></li>
        <li class="hover:bg-green-300 rounded-md ml-5 mr-5 p-1"><a href="{{route('botigues.select')}}">Modificar botigues</a></li>
    </ul>
</div>
    <h1 class="text-xl font-bold text-center">Modificar productes</h1>
@foreach ($productes as $producte)
    <div class="bg-green-200 hover:bg-green-400 p-2 m-2 rounded-sm">
        <a href="{{route('productes.edit', $producte->id)}} ">
        <p>{{$producte->nom}} {{$producte->mostra_prod ?" (s'està mostrant actualment)":" (no s'està mostrant actualment)"}}</p>
        <p>{{$producte->descripcio}}</p>
        </a>
    </div>
@endforeach
<a href="{{route('productes.create')}}"><h1 class="bg-green-200 hover:bg-green-400 p-2 m-2 rounded-sm text-center">Crear un producte nou</h1></a>
</x-layouts::app>