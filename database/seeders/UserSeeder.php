<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

/**
 *
 */
class UserSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        User::factory()->count(10)->create();
    }
}
