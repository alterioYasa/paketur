<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('users')->truncate();

        Schema::enableForeignKeyConstraints();

        User::create([
            'name' => "Super Admin",
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'Super Admin',
            'company_id' => null,
        ]);
    }
}
