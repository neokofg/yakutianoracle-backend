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
        $category = Category::firstOrCreate([
            'title' => strtolower($request->category)
        ]);
        $geo = $request->location;
        $nearestCity = City::select("id", DB::raw("geometry <-> ST_SetSRID(ST_MakePoint($geo[0],$geo[1]), 3857) AS distance"))
            ->orderBy('distance', 'ASC')
            ->first();
        Geo::create([
            'geometry' => new Point($geo[0],$geo[1]),
            'name' => $request->name,
            'category_id' => $category->id,
            'city_id' => $nearestCity?->id
        ]);
        return ['status' => true, 'message' => 'Импортировано успешно!'];
    }
}
