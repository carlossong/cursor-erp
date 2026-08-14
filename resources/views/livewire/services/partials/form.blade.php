<div class="space-y-6">
    <flux:input wire:model="form.code" :label="__('Código')" type="text" required autofocus />
    <flux:input wire:model="form.name" :label="__('Nome')" type="text" required />
    <flux:textarea wire:model="form.description" :label="__('Descrição')" rows="3" />

    <flux:select wire:model="form.category_id" :label="__('Categoria')">
        <flux:select.option value="">{{ __('Sem categoria') }}</flux:select.option>
        @foreach ($this->categories as $category)
            <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
        @endforeach
    </flux:select>

    <flux:select wire:model="form.unit" :label="__('Unidade')">
        @foreach (App\Enums\Unit::cases() as $unit)
            <flux:select.option :value="$unit->value">{{ $unit->label() }}</flux:select.option>
        @endforeach
    </flux:select>

    <flux:select wire:model="form.billing_mode" :label="__('Modo de faturamento')">
        @foreach (App\Enums\BillingMode::cases() as $mode)
            <flux:select.option :value="$mode->value">{{ $mode->label() }}</flux:select.option>
        @endforeach
    </flux:select>

    <flux:input.group :label="__('Preço padrão')">
        <flux:input.group.prefix>R$</flux:input.group.prefix>
        <flux:input wire:model="form.default_price" type="text" required />
    </flux:input.group>

    @if ($this->canViewCost)
        <flux:input.group :label="__('Custo padrão')">
            <flux:input.group.prefix>R$</flux:input.group.prefix>
            <flux:input wire:model="form.default_cost" type="text" required />
        </flux:input.group>
    @endif

    <flux:switch wire:model="form.is_active" :label="__('Ativo')" />
</div>
