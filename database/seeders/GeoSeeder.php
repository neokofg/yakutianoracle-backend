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
        $geojsonData = file_get_contents(storage_path('/app/public/tourism.geojson'));
        $geojsonData = json_decode($geojsonData, true);
        foreach ($geojsonData['features'] as $feature) {
            $properties = $feature['properties'];

            $coordinates = $feature['geometry']['coordinates'];
            $geo = new Geo();
            $geo->name = $properties['NAME'];
            $geo->geometry = new Point($coordinates[0],$coordinates[1]);
            $geo->category_id = $this->getCategory($properties);
            $geo->city_id = $this->calculateCity($coordinates);
            $geo->save();
        }
        $attractionsData = file_get_contents(storage_path('/app/public/attraction.geojson'));
        $attractionsData = json_decode($attractionsData, true);
        foreach ($attractionsData['features'] as $feature) {
            $properties = $feature['properties'];

            $coordinates = $feature['geometry']['coordinates'];
            $geo = new Geo();
            $geo->name = $properties['Наименование'] ?? null;
            $geo->geometry = new Point($coordinates[0],$coordinates[1]);
            $geo->category_id = Category::firstOrCreate([
                'title' => mb_strtolower($properties['Категория'])
            ], ['is_unique' => true])->id;
            $geo->city_id = $this->calculateCity($coordinates);
            $geo->save();
        }
    }

    private function getCategory($properties)
    {
        $categories = [
            'MAN_MADE',
            'LEISURE',
            'AMENITY',
            'OFFICE',
            'SHOP',
            'TOURISM',
            'SPORT'
        ];
        foreach ($categories as $property) {
            if(is_null($properties[$property])) {
                continue;
            }
            return Category::firstOrCreate([
                'title' => $properties[$property]
            ])->id;
        }
    }

    private function calculateCity(array $coordinates)
    {
        $lat = $coordinates[0];
        $lon = $coordinates[1];

        $nearestCity = City::select("id", DB::raw("geometry <-> ST_SetSRID(ST_MakePoint($lon, $lat), 3857) AS distance"))
            ->orderBy('distance', 'ASC')
            ->first();

        return $nearestCity?->id;
    }
}
