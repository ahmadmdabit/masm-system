<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
    	return [
    	    'device_uid' => $this->faker->uuid(),
    	    'app_id' => $this->faker->uuid(),
    	    'language' => $this->faker->languageCode(),
    	    'os' => $this->faker->randomElement([0 ,1]),
    	];
    }
}
