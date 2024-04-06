<?php

namespace App\Http\Controllers\Geo;

use App\Http\Controllers\Controller;
use App\Models\Geo;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $id)
    {
        Geo::find($id)->delete();

        return ['status' => 'true', 'message' => 'Удалено успешно!'];
    }
}
