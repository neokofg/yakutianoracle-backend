<?php

namespace App\Http\Controllers\Geo;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\City;
use App\Models\Geo;
use App\Presenters\JsonPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MStaack\LaravelPostgis\Geometries\Point;

class StoreController extends Controller
{
    public function __construct(
        private JsonPresenter $presenter
    )
    {
    }

    public function __invoke(Request $request)
    {
        $response = DB::transaction(function () use ($request) {
             return $this->importGeo($request);
        });

        return $this->presenter->present($response);
    }

    private function importGeo(Request $request)
    {
        $dataType = $request->dataType;
        switch ($dataType) {
            case 'one':
                return $this->importOneGeo($request);
            case 'multiple':
                return $this->importMultipleGeo($request);
            default:
                return ['status' => false, 'message' => 'Неверный dataType!'];
        }
    }

    private function importOneGeo(Request $request)
    {
        $category = Category::firstOrCreate([
            'title' => strtolower($request->category)
        ]);
        $city = City::firstOrCreate([
            'title' => strtolower($request->city)
        ]);
        [$lat,$lon] = $this->getLocation($request->location);
        Geo::create([
            'geometry' => new Point($lat,$lon),
            'name' => $request->name,
            'properties' => json_encode($request->properties),
            'category_id' => $category->id,
            'city_id' => $city->id
        ]);
        return ['status' => true, 'message' => 'Импортировано успешно!'];
    }

    private function importMultipleGeo(Request $request)
    {
        foreach ($request->geo as $geo) {
            $category = Category::firstOrCreate([
                'title' => strtolower($geo['category'])
            ]);
            $city = City::firstOrCreate([
                'title' => strtolower($geo['city'])
            ]);
            [$lat,$lon] = $this->getLocation($geo['location']);
            Geo::create([
                'geometry' => new Point($lat,$lon),
                'name' => $geo['name'],
                'properties' => json_encode($geo['properties']),
                'category_id' => $category->id,
                'city_id' => $city->id
            ]);
        }
        return ['status' => true, 'message' => 'Импортировано успешно!'];
    }

    private function getLocation(string $location)
    {
        $arr = explode(',', $location);
        $lat = floatval($arr[0]);
        $lon = floatval($arr[1]);
        return [$lat,$lon];
    }
}
