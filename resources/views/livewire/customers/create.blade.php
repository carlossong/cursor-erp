<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('Novo cliente') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Pessoa física ou jurídica, endereços e contatos.') }}</flux:text>
    </div>

    <form wire:submit="save" class="max-w-lg space-y-6">
        @include('livewire.customers.partials.form')

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            <flux:button :href="route('customers.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
        </div>
    </form>
</div>
