<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AllowanceType;

class AllowanceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = config('payroll.allowance_types', []);
        foreach ($types as $name) {
            AllowanceType::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}