<?php

namespace Database\Seeders;

use App\Models\Admin;
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
        Admin::updateOrCreate(
            ['mobile' => env('ADMIN_MOBILE', '09143322073')],
            ['name' => env('ADMIN_NAME', 'مدیر سیستم'), 'password' => env('ADMIN_PASSWORD', '123456'), 'is_active' => true],
        );
    }
}
