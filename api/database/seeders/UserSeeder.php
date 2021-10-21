<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Purchase;
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
        for ($ii=0; $ii < 10; $ii++) {
            User::factory()
                // ->count(10)
                ->has(Device::factory()->count(rand(1, 3)))
                ->has(Purchase::factory()->count(1))
                ->create();
        }
    }
}
