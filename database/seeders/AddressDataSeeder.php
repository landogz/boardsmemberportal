<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class AddressDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Barangay::truncate();
        City::truncate();
        Province::truncate();
        Region::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Import Regions
        $regionsPath = public_path('address/region.json');
        if (File::exists($regionsPath)) {
            $regions = json_decode(File::get($regionsPath), true);
            foreach ($regions as $region) {
                Region::create([
                    'id' => $region['id'],
                    'psgc_code' => $region['psgc_code'],
                    'region_name' => $region['region_name'],
                    'region_code' => $region['region_code'],
                ]);
            }
            $this->command->info('Regions imported: ' . count($regions));
        }

        // Import Provinces
        $provincesPath = public_path('address/province.json');
        if (File::exists($provincesPath)) {
            $provinces = json_decode(File::get($provincesPath), true);
            
            // Filter duplicates by province_code (keep first occurrence)
            $uniqueProvinces = [];
            $seenCodes = [];
            foreach ($provinces as $province) {
                $code = $province['province_code'];
                if (!isset($seenCodes[$code])) {
                    $seenCodes[$code] = true;
                    $uniqueProvinces[] = $province;
                }
            }
            
            $batch = [];
            foreach ($uniqueProvinces as $province) {
                $batch[] = [
                    'province_code' => $province['province_code'],
                    'province_name' => $province['province_name'],
                    'psgc_code' => $province['psgc_code'],
                    'region_code' => $province['region_code'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Insert in batches of 500
                if (count($batch) >= 500) {
                    Province::insert($batch);
                    $batch = [];
                }
            }
            if (!empty($batch)) {
                Province::insert($batch);
            }
            $this->command->info('Provinces imported: ' . count($uniqueProvinces) . ' (filtered from ' . count($provinces) . ' total)');
        }

        // Import Cities
        $citiesPath = public_path('address/city.json');
        if (File::exists($citiesPath)) {
            $cities = json_decode(File::get($citiesPath), true);
            
            // Filter duplicates by city_code (keep first occurrence)
            $uniqueCities = [];
            $seenCodes = [];
            foreach ($cities as $city) {
                $code = $city['city_code'];
                if (!isset($seenCodes[$code])) {
                    $seenCodes[$code] = true;
                    $uniqueCities[] = $city;
                }
            }
            
            $batch = [];
            foreach ($uniqueCities as $city) {
                $batch[] = [
                    'city_code' => $city['city_code'],
                    'city_name' => $city['city_name'],
                    'psgc_code' => $city['psgc_code'],
                    'province_code' => $city['province_code'],
                    'region_code' => $city['region_desc'] ?? $city['region_code'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Insert in batches of 500
                if (count($batch) >= 500) {
                    City::insert($batch);
                    $batch = [];
                }
            }
            if (!empty($batch)) {
                City::insert($batch);
            }
            $this->command->info('Cities imported: ' . count($uniqueCities) . ' (filtered from ' . count($cities) . ' total)');
        }

        // Import Barangays
        $barangaysPath = public_path('address/barangay.json');
        if (File::exists($barangaysPath)) {
            $barangays = json_decode(File::get($barangaysPath), true);
            
            // Filter duplicates by brgy_code (keep first occurrence)
            $uniqueBarangays = [];
            $seenCodes = [];
            foreach ($barangays as $barangay) {
                $code = $barangay['brgy_code'];
                if (!isset($seenCodes[$code])) {
                    $seenCodes[$code] = true;
                    $uniqueBarangays[] = $barangay;
                }
            }
            
            $batch = [];
            foreach ($uniqueBarangays as $barangay) {
                $batch[] = [
                    'brgy_code' => $barangay['brgy_code'],
                    'brgy_name' => $barangay['brgy_name'],
                    'city_code' => $barangay['city_code'],
                    'province_code' => $barangay['province_code'],
                    'region_code' => $barangay['region_code'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Insert in batches of 500
                if (count($batch) >= 500) {
                    Barangay::insert($batch);
                    $batch = [];
                }
            }
            if (!empty($batch)) {
                Barangay::insert($batch);
            }
            $this->command->info('Barangays imported: ' . count($uniqueBarangays) . ' (filtered from ' . count($barangays) . ' total)');
        }

        $this->command->info('Address data import completed!');
    }
}
