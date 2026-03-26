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
            [
                'name' => 'Tech Solutions Ltd',
                'category' => 'Technology',
                'email' => 'contact@techsolutions.com',
                'phone' => '+385 1 234 5678',
                'website' => 'https://techsolutions.com',
                'street' => 'Ilica 123',
                'postal_code' => '10000',
                'city' => 'Zagreb',
                'source_url' => 'https://example.com/tech-solutions',
                'scraped_at' => now(),
            ],
        ];

        foreach ($companies as $companyData) {
            Company::create($companyData);
        }

        $this->command->info('Created ' . count($companies) . ' companies.');
    }
}
