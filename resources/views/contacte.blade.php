<x-layouts::app>
    <div class="bg-green-200 max-w-4xl ml-auto mr-auto mt-12 mb-12 rounded-lg p-5">
        <form action="{{ route('contacte') }}" method="get">
            <label class="block py-2">
                <span class="font-bold text-gray-700">{{ __('Correu electrònic de contacte') }}
                    <br>
                    <input class="font-normal rounded-lg w-full" type="email" name="email"
                        placeholder="{{ __('hola@exemple.com') }}" id="" required>
            </label>
            <label class="block py-2">
                <span class="font-bold text-gray-700">{{ __('Títol del comentari') }}
                    <br>
                    <input class="font-normal rounded-lg w-full" type="text" name="titol" id="titolCom"
                        placeholder="{{ __('Tinc un problema amb...') }}" required maxlength="50">
            </label>
            <label class="block py-2">
                <span class="font-bold text-gray-700">{{ __('Descripció del comentari') }}
                    <br>
                    <textarea class="font-normal rounded-lg w-full" type="text" name="description" id="desCom"
                        placeholder="{{ __('Tinc un problema amb...') }}" required maxlength="1000"></textarea>
            </label>
            <div class="full-w justify-center text-center">
                <button class="bg-green-400 hover:bg-green-500 p-2 rounded-lg "
                    type="submit">{{ __('Enviar comentari') }}</button>
            </div>

        </form>
    </div>
</x-layouts::app>
