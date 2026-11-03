@assets
@vite('resources/js/passkeys.js')
@endassets

<div
    x-data="{
        supported: false,
        showForm: false,
        name: '',
        loading: false,
        error: null,
        updateSupport() {
            this.supported = Boolean(window.Passkeys?.isSupported());
        },
        init() {
            this.updateSupport();
            window.addEventListener('passkeys:ready', () => this.updateSupport(), { once: true });
        },
        async register() {
            if (!this.name.trim()) return;

            this.loading = true;
            this.error = null;

            try {
                await window.Passkeys.register({ name: this.name.trim() });
                this.name = '';
                this.showForm = false;
                await $wire.loadPasskeys();
            } catch (error) {
                if (!(error instanceof window.PasskeyUserCancelledError)) {
                    this.error = error?.message || String(error);
                }
            } finally {
                this.loading = false;
            }
        },
    }"
>
    <template x-if="!supported">
        <flux:text>{{ __('Passkeys are not supported in this browser.') }}</flux:text>
    </template>

    <template x-if="supported && !showForm">
        <flux:button variant="primary" icon="plus" x-on:click="showForm = true">
            {{ __('Add passkey') }}
        </flux:button>
    </template>

    <template x-if="supported && showForm">
        <div class="space-y-4 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 p-4">
            <flux:input
                label="{{ __('Passkey name') }}"
                x-model="name"
                placeholder="{{ __('e.g., MacBook Pro, iPhone') }}"
                x-on:keydown.enter.prevent="register()"
            />
            <flux:text class="!mt-1">{{ __('Give this passkey a name to help you identify it later.') }}</flux:text>

            <p x-show="error" x-text="error" x-cloak class="text-sm text-red-600 dark:text-red-400"></p>

            <div class="flex gap-2">
                <flux:button
                    variant="primary"
                    type="button"
                    x-on:click="register()"
                    x-bind:disabled="loading || !name.trim()"
                >
                    <span x-show="!loading">{{ __('Register passkey') }}</span>
                    <span x-show="loading" x-cloak>{{ __('Registering...') }}</span>
                </flux:button>
                <flux:button variant="ghost" type="button" x-on:click="showForm = false; name = ''; error = null">
                    {{ __('Cancel') }}
                </flux:button>
            </div>
        </div>
    </template>
</div>
