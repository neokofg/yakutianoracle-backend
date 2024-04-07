<?php

namespace App\Http\Controllers\City;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Presenters\JsonPresenter;
use Illuminate\Http\Request;
use MStaack\LaravelPostgis\Geometries\Point;

class GetController extends Controller
{
    public function __construct(
        private JsonPresenter $presenter
    )
    {
    }

    public function __invoke(Request $request)
    {
        $cities = City::query();

        if(isset($request->box)) {
            [$pt1, $pt2, $pt3, $pt4] = explode(';', $request->box);
            [$lat1, $lon1] = explode(',', $pt1);
            [$lat2, $lon2] = explode(',', $pt2);
            [$lat3, $lon3] = explode(',', $pt3);
            [$lat4, $lon4] = explode(',', $pt4);
            $cities = $cities->whereRaw("ST_CONTAINS(
                ST_SetSRID(
                    ST_MakePolygon(
                        ST_GeomFromText('LINESTRING($lon1 $lat1, $lon2 $lat2, $lon3 $lat3, $lon4 $lat4, $lon1 $lat1)')
                    ),
                    3857
                )::geometry,
                cities.geometry::geometry)");
        }
        foreach ($cities->get() as $city) {
            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $city->id,
                    'name' => $city->title,
                    'transport' => $city->transports()->first(),
                    'year_round_rating' => $city->year_round_rating,
                    'population' => $city->population,
                    'year_rounds' => $city->year_rounds,
                    'place' => $city->place,
                    'rating' => $city->rating
                ],
                'geometry' => $city->geometry
            ];
        }
        $data = [
            "type" => "FeatureCollection",
            "features" => $features
        ];
        return $this->presenter->present($data);
    }
}
