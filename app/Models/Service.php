<?php

namespace App\Models;

use App\Enums\BillingMode;
use App\Enums\Unit;
use Carbon\CarbonInterface;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $company_id
 * @property int|null $category_id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property Unit $unit
 * @property string $default_price
 * @property string $default_cost
 * @property BillingMode $billing_mode
 * @property bool $is_active
 * @property CarbonInterface|null $created_at
 * @property CarbonInterface|null $updated_at
 * @property CarbonInterface|null $deleted_at
 */
#[Fillable([
    'category_id',
    'code',
    'name',
    'description',
    'unit',
    'default_price',
    'default_cost',
    'billing_mode',
    'is_active',
])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
        'unit' => Unit::Hour->value,
        'billing_mode' => BillingMode::RequiresWorkOrder->value,
        'default_price' => '0.00',
        'default_cost' => '0.00',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit' => Unit::class,
            'billing_mode' => BillingMode::class,
            'default_price' => 'decimal:2',
            'default_cost' => 'decimal:2',
            'is_active' => 'boolean',
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
     * @return BelongsTo<ServiceCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
