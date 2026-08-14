<?php

namespace Database\Factories;

use App\Enums\BillingMode;
use App\Enums\Unit;
use App\Models\Company;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'category_id' => null,
            'code' => fake()->unique()->bothify('SRV-####'),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'unit' => Unit::Hour,
            'default_price' => fake()->randomFloat(2, 50, 800),
            'default_cost' => fake()->randomFloat(2, 10, 200),
            'billing_mode' => BillingMode::RequiresWorkOrder,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function immediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'billing_mode' => BillingMode::Immediate,
        ]);
    }
}
