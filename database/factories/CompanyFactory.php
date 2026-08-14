<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'legal_name' => fake()->company(),
            'trade_name' => fake()->optional()->company(),
            'tax_id' => fake()->unique()->numerify('##############'),
            'state_registration' => fake()->optional()->numerify('##########'),
            'municipal_registration' => fake()->optional()->numerify('########'),
            'email' => fake()->optional()->companyEmail(),
            'phone' => fake()->optional()->numerify('###########'),
            'address' => [
                'street' => fake()->streetName(),
                'number' => fake()->buildingNumber(),
                'complement' => fake()->optional()->secondaryAddress(),
                'district' => fake()->words(2, true),
                'city' => fake()->city(),
                'state' => fake()->regexify('[A-Z]{2}'),
                'zip' => fake()->numerify('########'),
            ],
            'logo_path' => null,
            'pix_key' => fake()->optional()->safeEmail(),
            'bank_details' => fake()->optional()->sentence(),
        ];
    }
}
