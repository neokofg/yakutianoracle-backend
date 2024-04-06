<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Presenters\JsonPresenter;
use Illuminate\Http\Request;

class GetController extends Controller
{
    public function __construct(
        private JsonPresenter $presenter
    )
    {
    }

    public function __invoke(Request $request, string $city_id)
    {
        $city = City::find($city_id);
        $data = [
            'title' => $city->title,
            'year_rounds' => $city->year_rounds,
            'geo' => $city->geos()->with(['category'])->get()->makeHidden(['category_id','city_id','type','id','geometry']),
            'transports' => $city->transports,
        ];
        return $this->presenter->present($data);
    }
}
