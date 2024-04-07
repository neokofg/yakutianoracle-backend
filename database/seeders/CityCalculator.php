<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CityCalculator extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (City::all() as $city) {
            $rating = 0;
            $year_round_rating = $city->year_round_rating * 10;
            $geos = $city->geos()->whereRelation('category','is_unique','=',false)->count() * 0.5;
            $attractions = $city->geos()->whereRelation('category','is_unique','=',true)->count() * 10;
            $transport = $city->transports->rating * 20;

            $rating = $year_round_rating + $geos + $attractions + $transport;
            $city->rating = $rating / 10;
            $city->save();
        }
    }
}
