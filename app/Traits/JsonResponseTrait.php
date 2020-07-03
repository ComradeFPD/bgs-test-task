<?php


namespace App\Traits;


trait JsonResponseTrait
{
    public function successResponse($data)
    {
        return response()->json($data, 200);
    }

    public function modelNotFoundResponse($data)
    {
        return response()->json($data, 404);
    }

    public function errorResponse($data)
    {
        return response()->json($data, 400);
    }
}
