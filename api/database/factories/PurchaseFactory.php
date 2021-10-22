<?php

namespace Database\Factories;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        $status = $this->faker->boolean();
    	return [
    	    'receipt' => $this->faker->uuid(),
    	    'state' => $this->faker->randomElement([0 ,1, 2]),
    	    'last_check_at' => $this->faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'),
    	    'status' => $status,
    	    'is_rate_limit' => !$status ? $this->faker->boolean() : false,
    	    'expire_date' => $this->faker->dateTimeBetween('-1 months', '+3 months')->format('Y-m-d H:i:s'),
    	];
    }
}
