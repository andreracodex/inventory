<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Kilogram'],
            ['name' => 'Liter'],
            ['name' => 'Piece'],
            ['name' => 'Meter'],
            ['name' => 'Gram'],
            // Add more units as needed
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
