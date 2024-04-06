<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Geo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MStaack\LaravelPostgis\Geometries\Point;

class GeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::create([
            'title' => 'Туризм'
        ]);
        $geojsonData = file_get_contents(storage_path('/app/public/tourism.geojson'));
        $geojsonData = json_decode($geojsonData, true);
        foreach ($geojsonData['features'] as $feature) {
            $properties = $feature['properties'];

            $coordinates = $feature['geometry']['coordinates'];
            $geo = new Geo();
            $geo->name = $properties['NAME'];
            $geo->geometry = new Point($coordinates[0],$coordinates[1]);
            $geo->category_id = $category->id;
            $geo->city_id = $this->calculateCity($coordinates);
            $geo->save();
        }
    }

    private function calculateCity(array $coordinates)
    {
        $lat = $coordinates[0];
        $lon = $coordinates[1];

        $nearestCity = City::select("id", DB::raw("geometry <-> ST_SetSRID(ST_MakePoint($lon, $lat), 4326) AS distance"))
            ->orderBy('distance', 'ASC')
            ->first();

        return $nearestCity?->id;
    }
}
