<div class="space-y-6">
    <flux:radio.group wire:model="form.person_type" :label="__('Tipo')" variant="segmented">
        <flux:radio value="pj">{{ __('Pessoa jurídica') }}</flux:radio>
        <flux:radio value="pf">{{ __('Pessoa física') }}</flux:radio>
    </flux:radio.group>

    <flux:input wire:model="form.name" :label="__('Nome / razão social')" type="text" required />
    <flux:input wire:model="form.tax_id" :label="__('CPF / CNPJ')" type="text" maxlength="14" />
    <flux:input wire:model="form.email" :label="__('E-mail')" type="email" />
    <flux:input wire:model="form.phone" :label="__('Telefone')" type="text" />
    <flux:textarea wire:model="form.notes" :label="__('Observações')" rows="3" />
    <flux:switch wire:model="form.is_active" :label="__('Ativo')" />

    <flux:separator />
    <flux:heading size="sm">{{ __('Endereço de cobrança') }}</flux:heading>
    @include('livewire.customers.partials.address', ['prefix' => 'form.billing_address'])

    <flux:separator />
    <flux:heading size="sm">{{ __('Endereço de atendimento') }}</flux:heading>
    @include('livewire.customers.partials.address', ['prefix' => 'form.service_address'])

    <flux:separator />
    <div class="flex items-center justify-between gap-4">
        <flux:heading size="sm">{{ __('Contatos') }}</flux:heading>
        <flux:button type="button" size="sm" wire:click="addContact">{{ __('Adicionar contato') }}</flux:button>
    </div>

    @foreach ($form->contacts as $index => $contact)
        <div wire:key="contact-{{ $index }}" class="space-y-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <flux:input wire:model="form.contacts.{{ $index }}.name" :label="__('Nome')" type="text" />
            <flux:input wire:model="form.contacts.{{ $index }}.role" :label="__('Cargo')" type="text" />
            <flux:input wire:model="form.contacts.{{ $index }}.email" :label="__('E-mail')" type="email" />
            <flux:input wire:model="form.contacts.{{ $index }}.phone" :label="__('Telefone')" type="text" />
            <flux:switch wire:model="form.contacts.{{ $index }}.is_primary" :label="__('Contato principal')" />

            @if (count($form->contacts) > 1)
                <flux:button type="button" variant="ghost" size="sm" wire:click="removeContact({{ $index }})">
                    {{ __('Remover') }}
                </flux:button>
            @endif
        </div>
    @endforeach
</div>
