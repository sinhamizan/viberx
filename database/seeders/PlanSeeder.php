<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Essential',
                'slug' => 'essential',
                'tagline' => 'For occasional use',
                'doses_per_month' => 8,
                'monthly_price_cents' => 11900,
                'quarterly_price_cents' => 29700,
                'is_recommended' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'tagline' => 'Our anchor tier',
                'doses_per_month' => 12,
                'monthly_price_cents' => 15900,
                'quarterly_price_cents' => 41700,
                'is_recommended' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Performance',
                'slug' => 'performance',
                'tagline' => 'Lowest cost per dose',
                'doses_per_month' => 18,
                'monthly_price_cents' => 20900,
                'quarterly_price_cents' => 56700,
                'is_recommended' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
