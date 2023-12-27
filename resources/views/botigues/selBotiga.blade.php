<x-guest-layout>
    <div class="bg-verd1">
    <ul class="flex p-2 text-top-center justify-center">
        <li class="hover:bg-verd3 rounded-md ml-5 mr-5 p-1"><a href="{{route('seccions.select')}}">Modificar seccions</a></li>
        <li class="hover:bg-verd3 rounded-md ml-5 mr-5 p-1"><a href="{{route('productes.select')}}">Modificar productes</a></li>
    </ul>
</div>
    <h1 class="text-xl font-bold text-center">Modificar botigues</h1>
@foreach ($botigues as $botiga)
    <div class="bg-verd2 hover:bg-verd4 p-2 m-2 rounded-sm">
        <a href="{{route('botigues.edit', $botiga->id)}}">
        <p>{{$botiga->poblacio}}</p>
        <p>{{$botiga->adreca}}</p>
        </a>
    </div>
@endforeach
</x-guest-layout>