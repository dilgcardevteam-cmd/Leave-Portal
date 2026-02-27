<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LeaveCategorySeeder::class,
        ]);
        if (!User::where('role', 'admin')->exists()) {
            User::create([
                'username' => 'admin',
                'first_name' => 'System',
                'middle_name' => null,
                'last_name' => 'Admin',
                'name' => 'System Admin',
                'email' => 'admin@example.com',
                'mobile_number' => null,
                'sex' => null,
                'region' => null,
                'province_office' => null,
                'position' => 'Administrator',
                'id_no' => 'ADMIN-0001',
                'role' => 'admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
