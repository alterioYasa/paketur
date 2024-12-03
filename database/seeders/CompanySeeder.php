<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('companies')->truncate();

        Schema::enableForeignKeyConstraints();

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
