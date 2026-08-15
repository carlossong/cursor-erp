<?php

namespace App\Models;

use App\Enums\PersonType;
use Carbon\CarbonInterface;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $company_id
 * @property PersonType $person_type
 * @property string $name
 * @property string|null $tax_id
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $notes
 * @property bool $is_active
 * @property array<string, string|null>|null $billing_address
 * @property array<string, string|null>|null $service_address
 * @property CarbonInterface|null $created_at
 * @property CarbonInterface|null $updated_at
 * @property CarbonInterface|null $deleted_at
 */
#[Fillable([
    'person_type',
    'name',
    'tax_id',
    'email',
    'phone',
    'notes',
    'is_active',
    'billing_address',
    'service_address',
])]
class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
        'person_type' => PersonType::PJ->value,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'person_type' => PersonType::class,
            'is_active' => 'boolean',
            'billing_address' => 'array',
            'service_address' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return HasMany<CustomerContact, $this>
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(CustomerContact::class)->chaperone();
    }

    /**
     * @return HasOne<CustomerContact, $this>
     */
    public function primaryContact(): HasOne
    {
        return $this->contacts()
            ->one()
            ->where('is_primary', true)
            ->withAttributes(['is_primary' => true]);
    }

    /**
     * @param  Builder<Customer>  $query
     * @return Builder<Customer>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
