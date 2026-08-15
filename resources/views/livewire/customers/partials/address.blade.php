<div class="space-y-4">
    <flux:input wire:model="{{ $prefix }}.street" :label="__('Logradouro')" type="text" required />
    <flux:input wire:model="{{ $prefix }}.number" :label="__('Número')" type="text" required />
    <flux:input wire:model="{{ $prefix }}.complement" :label="__('Complemento')" type="text" />
    <flux:input wire:model="{{ $prefix }}.district" :label="__('Bairro')" type="text" required />
    <flux:input wire:model="{{ $prefix }}.city" :label="__('Cidade')" type="text" required />
    <flux:input wire:model="{{ $prefix }}.state" :label="__('UF')" type="text" maxlength="2" required />
    <flux:input wire:model="{{ $prefix }}.zip" :label="__('CEP')" type="text" maxlength="8" required />
</div>
