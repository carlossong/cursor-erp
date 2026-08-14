<?php

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $legal_name
 * @property string|null $trade_name
 * @property string $tax_id
 * @property string|null $state_registration
 * @property string|null $municipal_registration
 * @property string|null $email
 * @property string|null $phone
 * @property array<string, string|null>|null $address
 * @property string|null $logo_path
 * @property int $default_quote_validity_days
 * @property string $max_discount_percent_sales
 * @property string $tax_rate
 * @property string|null $pix_key
 * @property string|null $bank_details
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'legal_name',
    'trade_name',
    'tax_id',
    'state_registration',
    'municipal_registration',
    'email',
    'phone',
    'address',
    'logo_path',
    'default_quote_validity_days',
    'max_discount_percent_sales',
    'tax_rate',
    'pix_key',
    'bank_details',
])]
class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'default_quote_validity_days' => 15,
        'max_discount_percent_sales' => '10.00',
        'tax_rate' => '0.00',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'address' => 'array',
            'default_quote_validity_days' => 'integer',
            'max_discount_percent_sales' => 'decimal:2',
            'tax_rate' => 'decimal:2',
        ];
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<Customer, $this>
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
