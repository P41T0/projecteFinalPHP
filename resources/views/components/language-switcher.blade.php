<flux:dropdown position="bottom" align="end">
    <flux:button variant="ghost" size="sm">
        {{ __('Language') }} · {{ strtoupper(app()->getLocale()) }}
    </flux:button>

    <flux:menu>
        <flux:menu.item :href="route('localization', ['idioma' => 'ca'])">Català</flux:menu.item>
        <flux:menu.item :href="route('localization', ['idioma' => 'en'])">English</flux:menu.item>
        <flux:menu.item :href="route('localization', ['idioma' => 'es'])">Español</flux:menu.item>
    </flux:menu>
</flux:dropdown>
