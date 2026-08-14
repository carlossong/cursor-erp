<?php

namespace App\Livewire\Forms;

use App\Enums\PersonType;
use App\Models\Customer;
use App\Models\User;
use App\Rules\BrazilianTaxId;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CustomerForm extends Form
{
    public string $person_type = PersonType::PJ->value;

    public string $name = '';

    public string $tax_id = '';

    public string $email = '';

    public string $phone = '';

    public string $notes = '';

    public bool $is_active = true;

    /**
     * @var array{street: string, number: string, complement: string, district: string, city: string, state: string, zip: string}
     */
    public array $billing_address = [
        'street' => '',
        'number' => '',
        'complement' => '',
        'district' => '',
        'city' => '',
        'state' => '',
        'zip' => '',
    ];

    /**
     * @var array{street: string, number: string, complement: string, district: string, city: string, state: string, zip: string}
     */
    public array $service_address = [
        'street' => '',
        'number' => '',
        'complement' => '',
        'district' => '',
        'city' => '',
        'state' => '',
        'zip' => '',
    ];

    /**
     * @var array<int, array{name: string, role: string, email: string, phone: string, is_primary: bool}>
     */
    public array $contacts = [];

    public function fillFrom(Customer $customer): void
    {
        $customer->loadMissing('contacts');

        $this->person_type = $customer->person_type->value;
        $this->name = $customer->name;
        $this->tax_id = $customer->tax_id ?? '';
        $this->email = $customer->email ?? '';
        $this->phone = $customer->phone ?? '';
        $this->notes = $customer->notes ?? '';
        $this->is_active = $customer->is_active;
        $this->billing_address = array_merge($this->billing_address, $customer->billing_address ?? []);
        $this->service_address = array_merge($this->service_address, $customer->service_address ?? []);
        $contacts = [];

        foreach ($customer->contacts as $contact) {
            $contacts[] = [
                'name' => $contact->name,
                'role' => $contact->role ?? '',
                'email' => $contact->email ?? '',
                'phone' => $contact->phone ?? '',
                'is_primary' => $contact->is_primary,
            ];
        }

        $this->contacts = $contacts;

        if ($this->contacts === []) {
            $this->contacts = [self::emptyContact(primary: true)];
        }
    }

    public function addContact(): void
    {
        $this->contacts[] = self::emptyContact();
    }

    public function removeContact(int $index): void
    {
        unset($this->contacts[$index]);
        $this->contacts = array_values($this->contacts);

        if ($this->contacts === []) {
            $this->contacts = [self::emptyContact(primary: true)];
        }
    }

    public function store(User $actor): Customer
    {
        $customer = new Customer;
        $customer->fill($this->validatedAttributes());
        $customer->company_id = $actor->company_id;
        $customer->save();
        $this->syncContacts($customer);

        return $customer;
    }

    public function update(Customer $customer): void
    {
        $customer->update($this->validatedAttributes());
        $this->syncContacts($customer);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $personType = PersonType::tryFrom($this->person_type) ?? PersonType::PJ;

        return [
            'person_type' => ['required', Rule::enum(PersonType::class)],
            'name' => ['required', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:14', new BrazilianTaxId($personType)],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'billing_address.street' => ['required', 'string', 'max:255'],
            'billing_address.number' => ['required', 'string', 'max:255'],
            'billing_address.complement' => ['nullable', 'string', 'max:255'],
            'billing_address.district' => ['required', 'string', 'max:255'],
            'billing_address.city' => ['required', 'string', 'max:255'],
            'billing_address.state' => ['required', 'string', 'size:2'],
            'billing_address.zip' => ['required', 'string', 'max:8'],
            'service_address.street' => ['required', 'string', 'max:255'],
            'service_address.number' => ['required', 'string', 'max:255'],
            'service_address.complement' => ['nullable', 'string', 'max:255'],
            'service_address.district' => ['required', 'string', 'max:255'],
            'service_address.city' => ['required', 'string', 'max:255'],
            'service_address.state' => ['required', 'string', 'size:2'],
            'service_address.zip' => ['required', 'string', 'max:8'],
            'contacts' => ['array'],
            'contacts.*.name' => ['nullable', 'string', 'max:255'],
            'contacts.*.role' => ['nullable', 'string', 'max:255'],
            'contacts.*.email' => ['nullable', 'email', 'max:255'],
            'contacts.*.phone' => ['nullable', 'string', 'max:255'],
            'contacts.*.is_primary' => ['boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedAttributes(): array
    {
        if ($this->service_address['street'] === '') {
            $this->service_address = $this->billing_address;
        }

        $validated = $this->validate();
        $validated['tax_id'] = $this->digits($validated['tax_id'] ?? null);
        unset($validated['contacts']);

        return $validated;
    }

    private function syncContacts(Customer $customer): void
    {
        $customer->contacts()->delete();

        $rows = collect($this->contacts)
            ->filter(fn (array $contact): bool => filled($contact['name']))
            ->values();

        $hasPrimary = $rows->contains(fn (array $contact): bool => $contact['is_primary']);

        foreach ($rows as $index => $contact) {
            $customer->contacts()->create([
                'name' => $contact['name'],
                'role' => $contact['role'] ?: null,
                'email' => $contact['email'] ?: null,
                'phone' => $contact['phone'] ?: null,
                'is_primary' => $hasPrimary ? (bool) $contact['is_primary'] : $index === 0,
            ]);
        }
    }

    /**
     * @return array{name: string, role: string, email: string, phone: string, is_primary: bool}
     */
    public static function emptyContact(bool $primary = false): array
    {
        return [
            'name' => '',
            'role' => '',
            'email' => '',
            'phone' => '',
            'is_primary' => $primary,
        ];
    }

    private function digits(?string $value): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $value) ?? '';

        return $digits === '' ? null : $digits;
    }
}
