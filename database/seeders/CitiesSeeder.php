<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;
use Illuminate\Support\Facades\DB;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\MultiPolygon;
use MStaack\LaravelPostgis\Geometries\Point;
use MStaack\LaravelPostgis\Geometries\Polygon;
use Symfony\Component\Uid\Ulid;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $geojsonData = file_get_contents(storage_path('/app/public/territory.geojson'));
        $geojsonData = json_decode($geojsonData, true);
        foreach ($geojsonData['features'] as $feature) {
            $properties = $feature['properties'];

            $coordinates = $feature['geometry']['coordinates'];
            $wkt = "MULTIPOLYGON(" . implode(',', array_map(function($polygon) {
                    return '(' . implode(',', array_map(function($linearRing) {
                            return '(' . implode(',', array_map(function($coordinate) {
                                    return implode(' ', $coordinate);
                                }, $linearRing)) . ')';
                        }, $polygon)) . ')';
                }, $coordinates)) . ')';

            $name = $properties['NAME'] ?? 'NULL'; // Если имя отсутствует, используется SQL NULL
            if ($name !== 'NULL') {
                $name = DB::connection()->getPdo()->quote($properties['NAME']);
            }
            $wkt = DB::connection()->getPdo()->quote($wkt);
            $ulid = DB::connection()->getPdo()->quote(strtolower(Ulid::generate()));

            $fid = $properties['fid'];
            $sql = "INSERT INTO regions (id, fid, title, geometry) VALUES ({$ulid},{$fid},{$name},ST_GeomFromText($wkt, 4326));";
            DB::statement($sql);
        }

        $citiesGeojsonData = file_get_contents(storage_path('/app/public/cities.geojson'));
        $citiesGeojsonData = json_decode($citiesGeojsonData, true);

        foreach ($citiesGeojsonData['features'] as $cityGeojsonData) {
            $properties = $cityGeojsonData['properties'];
            if($properties['PLACE'] == 'locality') {
                continue;
            }
            $coordinates = $cityGeojsonData['geometry']['coordinates'];
            $city = City::firstOrNew([
                'title' => $properties['NAME']
            ]);
            $city->fid = $properties['fid'];
            $city->title = $properties['NAME'];
            $city->place = $properties['PLACE'];
            $city->population = $city->population + $properties['POPULATION'] ?? 0;
            $city->geometry = new Point($coordinates[0],$coordinates[1]);
            $city->region_id = $this->getRegion($coordinates);
            $city->save();
         }
    }

    private function getRegion(array $coordinates)
    {
        $pointWkt = "POINT($coordinates[0] $coordinates[1])";

        $regionID = DB::select(
            "
                SELECT id
                FROM regions
                WHERE ST_Contains(
                    geometry::geometry,
                    ST_GeomFromText(:pointWkt, 4326)::geometry
                )
            ",
            ['pointWkt' => $pointWkt]
        );
        if ($regionID) {
            return $regionID[0]->id;
        } else {
            echo "Точка не принадлежит ни одному из регионов.";
        }
    }
}
