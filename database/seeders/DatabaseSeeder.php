<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt(12345678),
            'is_active' => 1,
            'user_type' => 'admin'
        ]);


        $this->call([
            CategorySeeder::class,
            UnitSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
