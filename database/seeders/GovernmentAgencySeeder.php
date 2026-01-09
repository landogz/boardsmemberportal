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
        // Delete all existing agencies first
        GovernmentAgency::truncate();
        
        $agencies = [
            [
                'name' => 'COMMISSION ON HIGHER EDUCATION',
                'code' => 'CHED',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'DEPARTMENT OF EDUCATION',
                'code' => 'DepEd',
                'description' => 'Education',
                'is_active' => true,
            ],
            [
                'name' => 'DEPARTMENT OF FINANCE',
                'code' => 'DOF',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'DEPARTMENT OF FOREIGN AFFAIRS',
                'code' => 'DFA',
                'description' => 'Foreign relations',
                'is_active' => true,
            ],
            [
                'name' => 'DEPARTMENT OF HEALTH',
                'code' => 'DOH',
                'description' => 'Health services',
                'is_active' => true,
            ],
            [
                'name' => 'DEPARTMENT OF JUSTICE',
                'code' => 'DOJ',
                'description' => 'Legal matters, including Bureau of Immigration, NBI, Corrections',
                'is_active' => true,
            ],
            [
                'name' => 'DEPARTMENT OF LABOR AND EMPLOYMENT',
                'code' => 'DOLE',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'DEPARTMENT OF NATIONAL DEFENSE',
                'code' => 'DND',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'DEPARTMENT OF THE INTERIOR AND LOCAL GOVERNMENT',
                'code' => 'DILG',
                'description' => 'Oversees LGUs, Police',
                'is_active' => true,
            ],
            [
                'name' => 'INTEGRATED BAR OF THE PHILIPPINES',
                'code' => 'IBP',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'NATIONAL BUREAU OF INVESTIGATION',
                'code' => 'NBI',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'NATIONAL YOUTH COMMISSION',
                'code' => 'NYC',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'NGO',
                'code' => 'NGO',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'PHILIPPINE DRUG ENFORCEMENT AGENCY',
                'code' => 'PDEA',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'PHILIPPINE NATIONAL POLICE',
                'code' => 'PNP',
                'description' => null,
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

