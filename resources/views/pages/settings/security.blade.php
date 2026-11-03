<?php

use Laravel\Passkeys\Actions\DeletePasskey;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Security settings')] class extends Component {
    #[Locked]
    public array $passkeys = [];

    #[Locked]
    public ?int $deletingPasskeyId = null;

    #[Locked]
    public string $deletingPasskeyName = '';

    public bool $showDeleteModal = false;

    public function mount(): void
    {
        $this->loadPasskeys();
    }

    public function loadPasskeys(): void
    {
        $this->passkeys = auth()->user()->passkeys()
            ->latest()
            ->get(['id', 'name', 'created_at', 'last_used_at'])
            ->map(fn ($passkey) => [
                'id' => $passkey->id,
                'name' => $passkey->name,
                'created_at' => $passkey->created_at->diffForHumans(),
                'last_used_at' => $passkey->last_used_at?->diffForHumans(),
            ])
            ->toArray();
    }

    public function confirmDelete(int $passkeyId): void
    {
        $passkey = auth()->user()->passkeys()->findOrFail($passkeyId);

        $this->deletingPasskeyId = $passkey->id;
        $this->deletingPasskeyName = $passkey->name;
        $this->showDeleteModal = true;
    }

    public function deletePasskey(DeletePasskey $deletePasskey): void
    {
        abort_if($this->deletingPasskeyId === null, 404);

        $passkey = auth()->user()->passkeys()->findOrFail($this->deletingPasskeyId);

        $deletePasskey(auth()->user(), $passkey);

        $this->closeDeleteModal();
        $this->loadPasskeys();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deletingPasskeyId = null;
        $this->deletingPasskeyName = '';
    }
}; ?>

<section class="w-full">
    <flux:heading level="2" class="sr-only">{{ __('Security settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Security')" :subheading="__('Manage passwordless sign-in with passkeys')">
        <div class="mt-6 space-y-6">
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                @forelse ($passkeys as $passkey)
                    <div class="flex items-center justify-between border-b border-zinc-200 p-4 last:border-b-0 dark:border-zinc-700">
                        <div>
                            <p class="font-medium">{{ $passkey['name'] }}</p>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('Added :time', ['time' => $passkey['created_at']]) }}
                                @if ($passkey['last_used_at'])
                                    · {{ __('Last used :time', ['time' => $passkey['last_used_at']]) }}
                                @endif
                            </p>
                        </div>

                        <flux:button
                            variant="ghost"
                            size="sm"
                            icon="trash"
                            wire:click="confirmDelete({{ $passkey['id'] }})"
                        >
                            {{ __('Remove') }}
                        </flux:button>
                    </div>
                @empty
                    <div class="p-6 text-center">
                        <flux:icon.key class="mx-auto size-7 text-zinc-400" />
                        <p class="mt-3 font-medium">{{ __('No passkeys yet') }}</p>
                        <flux:text class="mt-1">{{ __('Add a passkey to sign in without a password.') }}</flux:text>
                    </div>
                @endforelse
            </div>

            <x-passkey-registration />
        </div>
    </x-pages::settings.layout>

    <flux:modal name="delete-passkey-modal" class="max-w-md" wire:model="showDeleteModal">
        <div class="space-y-6">
            <div class="space-y-2">
                <flux:heading size="lg">{{ __('Remove passkey') }}</flux:heading>
                <flux:text>
                    {{ __('Are you sure you want to remove the passkey ":name"? You will no longer be able to use it to sign in.', ['name' => $deletingPasskeyName]) }}
                </flux:text>
            </div>

            <div class="flex justify-end gap-3">
                <flux:button variant="outline" wire:click="closeDeleteModal">{{ __('Cancel') }}</flux:button>
                <flux:button variant="danger" wire:click="deletePasskey">{{ __('Remove passkey') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
