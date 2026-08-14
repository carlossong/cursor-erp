<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div>
            <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Orçamento → ordem de serviço → faturamento → recebimento.') }}</flux:text>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <flux:card class="space-y-1">
                <flux:text class="text-sm">{{ __('Orçamentos abertos') }}</flux:text>
                <flux:heading size="lg">—</flux:heading>
            </flux:card>
            <flux:card class="space-y-1">
                <flux:text class="text-sm">{{ __('OS em andamento') }}</flux:text>
                <flux:heading size="lg">—</flux:heading>
            </flux:card>
            <flux:card class="space-y-1">
                <flux:text class="text-sm">{{ __('A receber') }}</flux:text>
                <flux:heading size="lg">—</flux:heading>
            </flux:card>
        </div>
    </div>
</x-layouts::app>
