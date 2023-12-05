<x-app-layout>
    <p>productes comprats</p>
    <ul>
    @forelse ($comanda->productes as $prod)
        <li>{{$prod->nom}} --> {{$prod->pivot->quantitat}}</li>
    @empty
        <li>No hi ha productes comprats</li>
    @endforelse
    </ul>
    <a href="">comprar</a>
</x-app-layout>