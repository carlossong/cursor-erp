<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('Novo usuário') }}</flux:heading>
        <flux:text class="mt-1">{{ __('O usuário entra verificado na mesma empresa, sem registro público.') }}</flux:text>
    </div>

    <form wire:submit="save" class="max-w-lg space-y-6">
        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus />
        <flux:input wire:model="email" :label="__('Email')" type="email" required />
        <flux:input wire:model="phone" :label="__('Telefone')" type="text" />
        <flux:input wire:model="password" :label="__('Password')" type="password" required viewable />
        <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" required viewable />
        <flux:select wire:model="role" :label="__('Papel')">
            <flux:select.option value="admin">{{ __('Admin') }}</flux:select.option>
            <flux:select.option value="comercial">{{ __('Comercial') }}</flux:select.option>
            <flux:select.option value="operacao">{{ __('Operação') }}</flux:select.option>
            <flux:select.option value="financeiro">{{ __('Financeiro') }}</flux:select.option>
            <flux:select.option value="gestor">{{ __('Gestor') }}</flux:select.option>
        </flux:select>
        <flux:switch wire:model="is_active" :label="__('Ativo')" />

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            <flux:button :href="route('users.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
        </div>
    </form>
</div>
