<?php

namespace App\Http\Controllers;

use App\Car;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use Illuminate\Http\JsonResponse;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $cars = auth()->user()->cars;
        return response()->json($cars);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCarRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCarRequest $request): JsonResponse
    {
        $data = $request->all();

        Car::create([
            'year' => $data['year'],
            'make' => $data['make'],
            'model' => $data['model'],
            'user_id' => auth()->id()
        ]);

        return response()->json();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Car  $car
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Car $car): JsonResponse
    {
        return response()->json($car);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Car  $car
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Car $car): JsonResponse
    {
        $car->delete();
        return response()->json();
    }
}
