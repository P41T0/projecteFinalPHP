<x-guest-layout>
    <ul class="flex">
        <li><a href="{{route('seccions.select')}}">Modificar seccions</a></li>
        <li><a href="{{route('botigues.select')}}">Modificar botigues</a></li>
    </ul>
    <h1 class="text-xl font-bold">Modificar producte</h1>
@foreach ($productes as $producte)
    <div class="bg-verd2 hover:bg-verd4 p-2 m-2 rounded-sm">
        <a href="{{route('productes.edit', $producte->id)}}">
        <p>{{$producte->nom}}</p>
        <p>{{$producte->descripcio}}</p>
        </a>
    </div>
@endforeach
<h1 class=""><a href="{{route('productes.create')}}">Crear un producte nou</a></h1>
</x-guest-layout>