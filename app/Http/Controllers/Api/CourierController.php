<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreCourierRequest;
use App\Http\Requests\UpdateCourierRequest;
use App\Http\Resources\CourierResource;
use App\Models\Courier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CourierController
{
    public function index(Request $permintaan): AnonymousResourceCollection
    {
        $ambilData = Courier::query()
            ->select(['id', 'nama', 'email', 'telepon', 'level', 'jenis_kendaraan', 'plat_nomor', 'aktif', 'created_at', 'updated_at'])
            ->search($permintaan->input('search'))
            ->filterLevel($permintaan->input('level'))
            ->applySort($permintaan->input('sort_by'), $permintaan->input('sort_dir'));

        return CourierResource::collection($ambilData->paginate($permintaan->integer('per_page', 15)));
    }

    public function store(StoreCourierRequest $permintaan): JsonResponse
    {
        $dataKurir = Courier::create($permintaan->validated());

        return (new CourierResource($dataKurir))->response()->setStatusCode(201);
    }

    public function show(Courier $dataKurir): CourierResource
    {
        return new CourierResource($dataKurir);
    }

    public function update(UpdateCourierRequest $permintaan, Courier $dataKurir): CourierResource
    {
        $dataKurir->update($permintaan->validated());

        return new CourierResource($dataKurir);
    }

    public function destroy(Courier $dataKurir): JsonResponse
    {
        $dataKurir->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Courier deleted successfully.',
        ]);
    }
}
