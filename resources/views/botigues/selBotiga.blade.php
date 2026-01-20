<x-layouts::app>
    <div class="bg-green-100">
    <ul class="flex flex-wrap p-2 text-top-center justify-center">
        <li class="hover:bg-green-300 rounded-md ml-5 mr-5 p-1"><a href="{{route('seccions.select')}}">Modificar seccions</a></li>
        <li class="hover:bg-green-300 rounded-md ml-5 mr-5 p-1"><a href="{{route('productes.select')}}">Modificar productes</a></li>
    </ul>
</div>
    <h1 class="text-xl font-bold text-center">Modificar botigues</h1>
@foreach ($botigues as $botiga)
    <div class="bg-green-200 hover:bg-green-400 p-2 m-2 rounded-sm">
        <a href="{{route('botigues.edit', $botiga->id)}}">
        <p>{{$botiga->poblacio}}</p>
        <p>{{$botiga->adreca}}</p>
        </a>
    </div>
@endforeach
</x-layouts::app>