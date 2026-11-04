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
        ];

        foreach ($companies as $company) {
            Company::firstOrCreate(
                ['name' => $company['name']],
                ['status' => $company['status']]
            );
        }
    }
}
