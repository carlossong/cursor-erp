<?php

namespace App\Livewire\Settings;

use App\Models\Company as CompanyModel;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Title('Empresa')]
class Company extends Component
{
    use WithFileUploads;

    public string $legal_name = '';

    public string $trade_name = '';

    public string $tax_id = '';

    public string $state_registration = '';

    public string $municipal_registration = '';

    public string $email = '';

    public string $phone = '';

    /**
     * @var array{street: string, number: string, complement: string, district: string, city: string, state: string, zip: string}
     */
    public array $address = [
        'street' => '',
        'number' => '',
        'complement' => '',
        'district' => '',
        'city' => '',
        'state' => '',
        'zip' => '',
    ];

    public int $default_quote_validity_days = 15;

    public string $max_discount_percent_sales = '10.00';

    public string $tax_rate = '0.00';

    public string $pix_key = '';

    public string $bank_details = '';

    public mixed $logo = null;

    public function mount(): void
    {
        $company = $this->record();

        $this->authorize('view', $company);

        $this->legal_name = $company->legal_name;
        $this->trade_name = $company->trade_name ?? '';
        $this->tax_id = $company->tax_id;
        $this->state_registration = $company->state_registration ?? '';
        $this->municipal_registration = $company->municipal_registration ?? '';
        $this->email = $company->email ?? '';
        $this->phone = $company->phone ?? '';
        $this->address = array_merge($this->address, $company->address ?? []);
        $this->default_quote_validity_days = $company->default_quote_validity_days;
        $this->max_discount_percent_sales = $company->max_discount_percent_sales;
        $this->tax_rate = $company->tax_rate;
        $this->pix_key = $company->pix_key ?? '';
        $this->bank_details = $company->bank_details ?? '';
    }

    public function save(): void
    {
        $company = $this->record();

        $this->authorize('update', $company);

        $validated = $this->validate($this->rules());

        if ($this->logo instanceof TemporaryUploadedFile) {
            $validated['logo_path'] = $this->logo->store('logos', 'public');
        }

        unset($validated['logo']);

        $company->update($validated);

        $this->reset('logo');

        Flux::toast(variant: 'success', text: __('Dados da empresa atualizados.'));
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(): array
    {
        return [
            'legal_name' => ['required', 'string', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['required', 'string', 'size:14', 'regex:/^\d{14}$/'],
            'state_registration' => ['nullable', 'string', 'max:255'],
            'municipal_registration' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address.street' => ['required', 'string', 'max:255'],
            'address.number' => ['required', 'string', 'max:255'],
            'address.complement' => ['nullable', 'string', 'max:255'],
            'address.district' => ['required', 'string', 'max:255'],
            'address.city' => ['required', 'string', 'max:255'],
            'address.state' => ['required', 'string', 'size:2'],
            'address.zip' => ['required', 'string', 'max:8'],
            'default_quote_validity_days' => ['required', 'integer', 'min:1', 'max:365'],
            'max_discount_percent_sales' => ['required', 'numeric', 'min:0', 'max:100'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'pix_key' => ['nullable', 'string', 'max:255'],
            'bank_details' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    #[Computed]
    public function record(): CompanyModel
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return CompanyModel::query()->whereKey($user->company_id)->firstOrFail();
    }

    #[Computed]
    public function canUpdate(): bool
    {
        return Auth::user()?->can('update', $this->record()) ?? false;
    }
}
