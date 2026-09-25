<?php

namespace App\DataMigrations;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\Packages\WtfFinance\Models\PricingDimension;
use Dpb\Packages\WtfFinance\Models\PricingGroup;
use Dpb\Packages\WtfFinance\Models\PricingRule;
use Illuminate\Support\Facades\DB;

class WtfFinanceMigration implements DataMigration
{
    public function run(): void
    {
        // add pricing dimensions
        $dimensionIds = [];
        $dimensions = [
            ['code' => 'meter', 'title' => 'Meter', 'unit' => 'm'],
            ['code' => 'seat', 'title' => 'Sedadlo', 'unit' => 'sedadlo'],
            ['code' => 'unit', 'title' => 'Kus', 'unit' => 'ks'],
            ['code' => 'vehicle', 'title' => 'Vozidlo', 'unit' => 'vozidlo'],
        ];
        foreach ($dimensions as $dimension) {
            $dimensionIds[$dimension['code']] = PricingDimension::firstOrCreate($dimension)->id;
        }

        // add pricing rules
        $ruleIds = [];
        $rules = [
            ['code' => 'per_meter', 'title' => 'za meter', 'dimension_id' => $dimensionIds['meter']],
            ['code' => 'per_seats', 'title' => 'za sedadlo', 'dimension_id' => $dimensionIds['seat']],
            ['code' => 'per_subject', 'title' => 'za vozidlo', 'dimension_id' => $dimensionIds['vehicle']],
        ];
        foreach ($rules as $rule) {
            $ruleIds[$rule['code']] = PricingRule::firstOrCreate($rule)->id;
        }

        // add pricing groups
        $groupIds = [];
        $validFrom = '2026-01-01';
        $currency = 'EUR';
        $groups = [
            [
                'code' => 'okna',
                'title' => 'Okná',
                'rule_id' => $ruleIds['per_meter'],
                'unit_price' => 0.2,
                'currency' => $currency,
                'valid_from' => $validFrom,
            ],
            [
                'code' => 'strop.klimatizacia',
                'title' => 'Strop a klimatizácia',
                'rule_id' => $ruleIds['subject_length'],
                'unit_price' => 0.3,
                'currency' => $currency,
                'valid_from' => $validFrom,
            ],
            [
                'code' => 'tepovanie',
                'title' => 'Tepovanie',
                'rule_id' => $ruleIds['per_seats'],
                'unit_price' => 0.18,
                'currency' => $currency,
                'valid_from' => $validFrom,
            ],
            [
                'code' => 'grafity',
                'title' => 'Grafity',
                'rule_id' => $ruleIds['per_seats'],
                // 'unit_price' => 0.18,
                'currency' => $currency,
                'valid_from' => $validFrom,
            ],
        ];
        foreach ($groups as $group) {
            $groupIds[$group['code']] = PricingGroup::firstOrCreate($group)->id;
        }

        // add pricing group operations
        $pgOperations = [
            [
                'pricing_group_id' => 3,
                'operation_id' => 290019271,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('dpb_wtf_fin_pricing_group_operations')
            ->insert($pgOperations);
    }
}
