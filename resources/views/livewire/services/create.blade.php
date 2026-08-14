<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('Novo serviço') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Código, unidade, preço de tabela e modo de faturamento.') }}</flux:text>
    </div>

    <form wire:submit="save" class="max-w-lg space-y-6">
        @include('livewire.services.partials.form')

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            <flux:button :href="route('services.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
        </div>
    </form>
</div>
