<x-guest-layout>
    <ul class="flex">
        <li><a href="{{route('productes.select')}}">Modificar productes</a></li>
        <li><a href="">Modificar botigues</a></li>
    </ul>
    <h1 class="text-xl font-bold">Modificar seccions</h1>
@foreach ($seccions as $seccio)
    <div class="bg-verd2 hover:bg-verd4 p-2 m-2 rounded-sm">
        <a href="{{route('seccions.edit', $seccio->id)}}">
        <p>{{$seccio->nom}}</p>
        <p>{{$seccio->descripcio}}</p>
        </a>
    </div>
@endforeach
<h1 class=""><a href="{{route('seccions.create')}}">Crear una secció nova</a></h1>
</x-guest-layout>