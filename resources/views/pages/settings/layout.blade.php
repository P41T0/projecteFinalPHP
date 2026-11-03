<div class="mx-auto w-full max-w-6xl">
    <div class="mb-8">
        <flux:heading size="xl" class="tracking-tight">{{ __('Settings') }}</flux:heading>
        <flux:subheading>{{ __('Manage your account settings and preferences.') }}</flux:subheading>
    </div>

    <div class="grid gap-6 lg:grid-cols-[15rem_minmax(0,1fr)]">
        <aside class="h-fit rounded-2xl border border-green-200 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <flux:navlist aria-label="{{ __('Settings') }}">
                <flux:navlist.item icon="user" :href="route('profile.edit')" :current="request()->routeIs('profile.edit')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
                <flux:navlist.item icon="key" :href="route('user-password.edit')" :current="request()->routeIs('user-password.edit')" wire:navigate>{{ __('Password') }}</flux:navlist.item>
                @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                    <flux:navlist.item icon="shield-check" :href="route('two-factor.show')" :current="request()->routeIs('two-factor.show')" wire:navigate>{{ __('Two-Factor Auth') }}</flux:navlist.item>
                @endif
                @if (Laravel\Fortify\Features::canManagePasskeys())
                    <flux:navlist.item icon="finger-print" :href="route('security.edit')" :current="request()->routeIs('security.edit')" wire:navigate>{{ __('Security') }}</flux:navlist.item>
                @endif
                <flux:navlist.item icon="paint-brush" :href="route('appearance.edit')" :current="request()->routeIs('appearance.edit')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
            </flux:navlist>
        </aside>

        <section class="min-w-0 rounded-2xl border border-green-200 bg-white p-5 shadow-sm sm:p-8 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 pb-5 dark:border-zinc-800">
                <flux:heading size="lg">{{ $heading ?? '' }}</flux:heading>
                <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>
            </div>
            <div class="mt-6 w-full max-w-2xl">
                {{ $slot }}
            </div>
        </section>
    </div>
</div>
