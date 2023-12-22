<x-guest-layout>
    <ul class="flex">
        <li><a href="{{route('seccions.select')}}">Modificar seccions</a></li>
        <li><a href="{{route('productes.select')}}">Modificar productes</a></li>
    </ul>
    <h1 class="text-xl font-bold">Modificar botigues</h1>
@foreach ($botigues as $botiga)
    <div class="bg-verd2 hover:bg-verd4 p-2 m-2 rounded-sm">
        <a href="{{route('botigues.edit', $botiga->id)}}">
        <p>{{$botiga->poblacio}}</p>
        <p>{{$botiga->adreca}}</p>
        </a>
    </div>
@endforeach
</x-guest-layout>