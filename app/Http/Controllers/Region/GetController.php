<?php

namespace App\Http\Controllers\Region;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Presenters\JsonPresenter;
use Illuminate\Http\Request;

class GetController extends Controller
{
    public function __construct(
        private JsonPresenter $presenter
    )
    {
    }

    public function __invoke()
    {
        $regions = Region::all();
        $features = [];
        foreach ($regions as $region) {
            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'fid' => $region->fid,
                    'name' => $region->title
                ],
                'geometry' => $region->geometry
            ];
        }
        $data = [
            "type" => "FeatureCollection",
            "features" => $features
        ];
        return $this->presenter->present($data);
    }
}
