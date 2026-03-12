<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'tanvir2sky@gmail.com',
        ], [
            'name' => 'MD Tanvir Hossain',
            'password' => Hash::make('Tanvir@Admin2026!'),
        ]);

        SiteSetting::query()->updateOrCreate([
            'id' => 1,
        ], [
            'enable_gtm' => false,
            'gtm_id' => null,
            'enable_ga' => false,
            'ga_measurement_id' => null,
            'enable_clarity' => false,
            'clarity_project_id' => null,
        ]);
    }
}
