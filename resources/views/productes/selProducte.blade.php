<x-guest-layout>
    <div class="bg-verd1">
    <ul class="flex p-2 text-top-center justify-center">
        <li class="hover:bg-verd3 rounded-md ml-5 mr-5 p-1"><a href="{{route('seccions.select')}}">Modificar seccions</a></li>
        <li class="hover:bg-verd3 rounded-md ml-5 mr-5 p-1"><a href="{{route('botigues.select')}}">Modificar botigues</a></li>
    </ul>
</div>
    <h1 class="text-xl font-bold text-center">Modificar producte</h1>
@foreach ($productes as $producte)
    <div class="bg-verd2 hover:bg-verd4 p-2 m-2 rounded-sm">
        <a href="{{route('productes.edit', $producte->id)}} ">
        <p>{{$producte->nom}} {{$producte->mostra_prod ?" (s'està mostrant actualment)":" (no s'esta mostrant actualment)"}}</p>
        <p>{{$producte->descripcio}}</p>
        </a>
    </div>
@endforeach
<a href="{{route('productes.create')}}"><h1 class="bg-verd2 hover:bg-verd4 p-2 m-2 rounded-sm text-center">Crear un producte nou</h1></a>
</x-guest-layout>