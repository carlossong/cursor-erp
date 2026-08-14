<?php

namespace Database\Factories;

use App\Enums\PersonType;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'person_type' => PersonType::PJ,
            'name' => fake()->company(),
            'tax_id' => null,
            'email' => fake()->optional()->companyEmail(),
            'phone' => fake()->optional()->numerify('###########'),
            'notes' => fake()->optional()->sentence(),
            'is_active' => true,
            'billing_address' => self::address(),
            'service_address' => self::address(),
        ];
    }

    public function pf(): static
    {
        return $this->state(fn (array $attributes) => [
            'person_type' => PersonType::PF,
            'name' => fake()->name(),
            'tax_id' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * @return array{street: string, number: string, complement: string|null, district: string, city: string, state: string, zip: string}
     */
    public static function address(): array
    {
        return [
            'street' => fake()->streetName(),
            'number' => fake()->buildingNumber(),
            'complement' => fake()->optional()->word(),
            'district' => fake()->words(2, true),
            'city' => fake()->city(),
            'state' => fake()->regexify('[A-Z]{2}'),
            'zip' => fake()->numerify('########'),
        ];
    }
}
