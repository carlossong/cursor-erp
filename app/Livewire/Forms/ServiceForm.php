<?php

namespace App\Livewire\Forms;

use App\Enums\BillingMode;
use App\Enums\Unit;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ServiceForm extends Form
{
    public ?int $serviceId = null;

    public ?string $category_id = null;

    public string $code = '';

    public string $name = '';

    public string $description = '';

    public string $unit = Unit::Hour->value;

    public string $default_price = '0.00';

    public string $default_cost = '0.00';

    public string $billing_mode = BillingMode::RequiresWorkOrder->value;

    public bool $is_active = true;

    public function fillFrom(Service $service, bool $canViewCost = true): void
    {
        $this->serviceId = $service->id;
        $this->category_id = $service->category_id !== null ? (string) $service->category_id : null;
        $this->code = $service->code;
        $this->name = $service->name;
        $this->description = $service->description ?? '';
        $this->unit = $service->unit->value;
        $this->default_price = $service->default_price;
        $this->billing_mode = $service->billing_mode->value;
        $this->is_active = $service->is_active;

        if ($canViewCost) {
            $this->default_cost = $service->default_cost;
        }
    }

    public function store(User $actor): Service
    {
        $service = new Service;
        $service->fill($this->validatedAttributes($actor));
        $service->company_id = $actor->company_id;
        $service->save();

        return $service;
    }

    public function update(Service $service, User $actor): void
    {
        $service->update($this->validatedAttributes($actor));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return [
            'category_id' => [
                'nullable',
                Rule::exists(ServiceCategory::class, 'id')->where('company_id', $user->company_id),
            ],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Service::class, 'code')
                    ->where('company_id', $user->company_id)
                    ->ignore($this->serviceId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit' => ['required', Rule::enum(Unit::class)],
            'default_price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'default_cost' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'billing_mode' => ['required', Rule::enum(BillingMode::class)],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedAttributes(User $actor): array
    {
        if ($this->category_id === '' || $this->category_id === '0') {
            $this->category_id = null;
        }

        $validated = $this->validate();
        $validated['category_id'] = filled($validated['category_id'] ?? null)
            ? (int) $validated['category_id']
            : null;
        $validated['description'] = filled($validated['description'] ?? null)
            ? $validated['description']
            : null;

        if ($actor->cannot('services.view-cost')) {
            unset($validated['default_cost']);
        }

        return $validated;
    }
}
