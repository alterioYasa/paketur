<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'name' => 'Company A',
            'email' => 'contact@companya.com',
            'phone_number' => '081234567890',
        ]);

        Company::create([
            'name' => 'Company B',
            'email' => 'info@companyb.com',
            'phone_number' => '081987654321',
        ]);
    }
}
