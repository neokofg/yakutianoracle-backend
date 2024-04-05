<?php declare(strict_types=1);

namespace App\Presenters;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonPresenter
{
    public function present(mixed $response): JsonResponse
    {
        return response()->json($response)->setStatusCode(Response::HTTP_OK);
    }
}
