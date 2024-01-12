<x-guest-layout>
<div class="bg-verd2 max-w-4xl ml-auto mr-auto mt-12 mb-12 rounded-lg p-5">
    <form action="{{route('contacte')}}" method="get">
        <label class="block py-2">
            <span class="font-bold text-gray-700">{{__("Correu electrònic de contacte")}}
                <br>
                <input class="font-normal rounded-lg w-full" type="email" name="email" placeholder="{{__("hola@exemple.com")}}" id="" required>
        </label>
        <label class="block py-2">
            <span class="font-bold text-gray-700">{{__("Títol del comentari")}}
                <br>
                <input class="font-normal rounded-lg w-full" type="text" name="titol" id="titolCom" placeholder="{{__("Tinc un problema amb...")}}" required maxlength="50">
        </label>
        <label class="block py-2">
            <span class="font-bold text-gray-700">{{__("Descripció del comentari")}}
                <br>
                <textarea class="font-normal rounded-lg w-full" type="text" name="description" id="desCom" placeholder="{{__("Tinc un problema amb...")}}" required maxlength="1000"></textarea>
        </label>
        
        <button class="hover:bg-verd4 p-2 rounded-lg justify-center " type="submit">{{ __('Enviar comentari') }}</button>
    </form>
</div>
</x-guest-layout>