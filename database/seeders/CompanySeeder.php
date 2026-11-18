<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            ['name' => 'Northstar Labs', 'status' => 'active'],
            ['name' => 'BluePeak Studio', 'status' => 'active'],
            ['name' => 'SummitWorks', 'status' => 'active'],
            ['name' => 'Riverstone Group', 'status' => 'inactive'],
            ['name' => 'Orthstar Labs', 'status' => 'active'],
            ['name' => 'Peak Studio', 'status' => 'active'],
            ['name' => 'Summit Sun', 'status' => 'active'],
            ['name' => 'Noman Group', 'status' => 'active'],
            ['name' => 'Nthstar Labs', 'status' => 'active'],
            ['name' => 'Blue Studio', 'status' => 'active'],
            ['name' => 'Sit Works', 'status' => 'active'],
            ['name' => 'Rstone Group', 'status' => 'active'],
        ];

        foreach ($companies as $company) {
            Company::firstOrCreate(
                ['name' => $company['name']],
                ['status' => $company['status']]
            );
        }
    }
}
