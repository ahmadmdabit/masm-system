<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->count(10)
            ->has(Device::factory()->count(rand(1, 3)))
            ->create();
    }
}
