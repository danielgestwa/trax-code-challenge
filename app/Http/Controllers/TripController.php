<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use Illuminate\Http\JsonResponse;
use App\Trip;
use Carbon\Carbon;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $trips = auth()->user()->trips()->with('car')->get();
        return response()->json($trips);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTripRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTripRequest $request): JsonResponse
    {
        $data = $request->all();
        
        $lastTrip = auth()->user()->lastTrip($data['car_id']);
        $total = $data['miles'];
        if(isset($lastTrip)) {
            $total += $lastTrip->total;
        }

        Trip::create([
            'date' => Carbon::parse($data['date'])->format('Y-m-d'),
            'car_id' => $data['car_id'],
            'miles' => $data['miles'],
            'total' => $total
        ]);

        return response()->json();
    }
}
