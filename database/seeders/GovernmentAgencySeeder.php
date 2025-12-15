<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GovernmentAgency;

class GovernmentAgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agencies = [
            
            [
                'name' => 'Department of Health',
                'code' => 'DOH',
                'description' => 'Health services',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Trade and Industry',
                'code' => 'DTI',
                'description' => 'Business and trade',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Science and Technology',
                'code' => 'DOST',
                'description' => 'Science & technology',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Foreign Affairs',
                'code' => 'DFA',
                'description' => 'Foreign relations',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Justice',
                'code' => 'DOJ',
                'description' => 'Legal matters, including Bureau of Immigration, NBI, Corrections',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Interior and Local Government',
                'code' => 'DILG',
                'description' => 'Oversees LGUs, Police',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Social Welfare and Development',
                'code' => 'DSWD',
                'description' => 'Social welfare programs',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Transportation',
                'code' => 'DOTr',
                'description' => 'Transport infrastructure and services',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Environment and Natural Resources',
                'code' => 'DENR',
                'description' => 'Environment',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Education',
                'code' => 'DepEd',
                'description' => 'Education',
                'is_active' => true,
            ],
        ];

        foreach ($agencies as $agency) {
            GovernmentAgency::updateOrCreate(
                ['code' => $agency['code']],
                $agency
            );
        }
    }
}

