<x-layouts::app>
    <div class="mx-auto my-12 max-w-4xl rounded-2xl border border-green-200 bg-white p-6 shadow-sm sm:p-8 dark:border-zinc-800 dark:bg-zinc-900">
        <h1 class="mb-6 text-2xl font-semibold tracking-tight">{{ __('Informació de contacte') }}</h1>
        <form action="{{ route('contacte') }}" method="get">
            <label class="block py-2">
                <span class="font-bold text-gray-700 dark:text-zinc-200">{{ __('Correu electrònic de contacte') }}
                    <br>
                    <input class="font-normal rounded-lg w-full" type="email" name="email"
                        placeholder="{{ __('hola@exemple.com') }}" id="" required>
            </label>
            <label class="block py-2">
                <span class="font-bold text-gray-700 dark:text-zinc-200">{{ __('Títol del comentari') }}
                    <br>
                    <input class="font-normal rounded-lg w-full" type="text" name="titol" id="titolCom"
                        placeholder="{{ __('Tinc un problema amb...') }}" required maxlength="50">
            </label>
            <label class="block py-2">
                <span class="font-bold text-gray-700 dark:text-zinc-200">{{ __('Descripció del comentari') }}
                    <br>
                    <textarea class="font-normal rounded-lg w-full" type="text" name="description" id="desCom"
                        placeholder="{{ __('Tinc un problema amb...') }}" required maxlength="1000"></textarea>
            </label>
            <div class="full-w justify-center text-center">
                <button class="rounded-lg bg-green-700 px-5 py-2.5 font-medium text-white transition hover:bg-green-800 dark:bg-green-400 dark:text-green-950 dark:hover:bg-green-300"
                    type="submit">{{ __('Enviar comentari') }}</button>
            </div>

        </form>
    </div>
</x-layouts::app>
