<x-guest-layout>
    <div class="bg-verd1 justify-center">
    <ul class="flex flex-wrap p-2 text-top-center justify-center">
        <li class="hover:bg-verd3 rounded-md ml-5 mr-5 p-1"><a href="{{route('productes.select')}}">Modificar productes</a></li>
        <li class="hover:bg-verd3 rounded-md ml-5 mr-5 p-1"><a href="{{route('botigues.select')}}">Modificar botigues</a></li>
    </ul>
</div>
    <h1 class="text-xl font-bold text-center">Modificar seccions</h1>
@foreach ($seccions as $seccio)
    <div class="bg-verd2 hover:bg-verd4 p-2 m-2 rounded-sm">
        <a href="{{route('seccions.edit', $seccio->id)}}">
        <p>{{$seccio->nom}} {{$seccio->mostra_sec?" (s'està mostrant actualment)":" (no s'esta mostrant actualment)"}}</p>
        <p>{{$seccio->descripcio}}</p>
        </a>
    </div>
@endforeach
<a href="{{route('seccions.create')}}"><h1 class="bg-verd2 hover:bg-verd4 p-2 m-2 rounded-sm text-center">Crear una secció nova</h1></a>
</x-guest-layout>