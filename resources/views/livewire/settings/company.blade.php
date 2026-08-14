<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Empresa') }}</flux:heading>

    <x-settings.layout :heading="__('Empresa')" :subheading="__('Razão social, CNPJ, endereço e dados comerciais')">
        <form wire:submit="save" class="my-6 w-full space-y-6">
            <flux:input wire:model="legal_name" :label="__('Razão social')" type="text" required />
            <flux:input wire:model="trade_name" :label="__('Nome fantasia')" type="text" />
            <flux:input wire:model="tax_id" :label="__('CNPJ')" type="text" maxlength="14" required />
            <flux:input wire:model="state_registration" :label="__('Inscrição estadual')" type="text" />
            <flux:input wire:model="municipal_registration" :label="__('Inscrição municipal')" type="text" />
            <flux:input wire:model="email" :label="__('E-mail')" type="email" />
            <flux:input wire:model="phone" :label="__('Telefone')" type="text" />

            <flux:separator />

            <flux:heading size="sm">{{ __('Endereço') }}</flux:heading>
            <flux:input wire:model="address.street" :label="__('Logradouro')" type="text" required />
            <flux:input wire:model="address.number" :label="__('Número')" type="text" required />
            <flux:input wire:model="address.complement" :label="__('Complemento')" type="text" />
            <flux:input wire:model="address.district" :label="__('Bairro')" type="text" required />
            <flux:input wire:model="address.city" :label="__('Cidade')" type="text" required />
            <flux:input wire:model="address.state" :label="__('UF')" type="text" maxlength="2" required />
            <flux:input wire:model="address.zip" :label="__('CEP')" type="text" maxlength="8" required />

            <flux:separator />

            <flux:input wire:model="default_quote_validity_days" :label="__('Validade padrão do orçamento (dias)')" type="number" min="1" required />
            <flux:input wire:model="max_discount_percent_sales" :label="__('Teto de desconto comercial (%)')" type="number" step="0.01" min="0" required />
            <flux:input wire:model="tax_rate" :label="__('Alíquota (%)')" type="number" step="0.01" min="0" required />
            <flux:input wire:model="pix_key" :label="__('Chave Pix')" type="text" />
            <flux:textarea wire:model="bank_details" :label="__('Dados bancários')" rows="3" />
            <flux:input wire:model="logo" :label="__('Logotipo')" type="file" />

            @if ($this->canUpdate)
                <div class="flex items-center gap-4">
                    <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                </div>
            @endif
        </form>
    </x-settings.layout>
</section>
