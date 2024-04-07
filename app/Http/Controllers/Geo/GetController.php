<?php

namespace App\Http\Controllers\Geo;

use App\Http\Controllers\Controller;
use App\Models\Geo;
use App\Presenters\JsonPresenter;
use Illuminate\Http\Request;

class GetController extends Controller
{
    public function __construct(
        private JsonPresenter $presenter
    )
    {
    }

    public function __invoke(Request $request)
    {
        $geo = Geo::query();
        if(isset($request->box)) {
            [$pt1, $pt2, $pt3, $pt4] = explode(';', $request->box);
            [$lat1, $lon1] = explode(',', $pt1);
            [$lat2, $lon2] = explode(',', $pt2);
            [$lat3, $lon3] = explode(',', $pt3);
            [$lat4, $lon4] = explode(',', $pt4);
            $geo = $geo->whereRaw("ST_CONTAINS(
                ST_SetSRID(
                    ST_MakePolygon(
                        ST_GeomFromText('LINESTRING($lon1 $lat1, $lon2 $lat2, $lon3 $lat3, $lon4 $lat4, $lon1 $lat1)')
                    ),
                    3857
                )::geometry,
                geo.geometry::geometry)");
        }
        if(isset($request->category_id)) {
            $geo = $geo->where('category_id', '=', $request->category_id);
        }
        if(isset($request->city_id)) {
            $geo = $geo->where('city_id', '=', $request->city_id);
        }
        if(isset($request->search)) {
            $geo = $geo->whereRelation('category', 'title', 'ILIKE', '%'.$request->search.'%')
                ->orWhere('name','ILIKE','%'.$request->search.'%');
        }
        $data = [
            "type" => "FeatureCollection",
            "features" => $geo->get()->makeHidden(['category_id','city_id','name','category'])
        ];

        return $this->presenter->present($data);
    }
}
