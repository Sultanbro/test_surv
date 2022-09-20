<?php

namespace App\Http\Controllers;

use App\Models\AwardType;
use App\Http\Requests\StoreAwardTypeRequest;
use App\Http\Requests\UpdateAwardTypeRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AwardTypeController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $awardTypes = AwardType::all();

            return response()->json($awardTypes);
        } catch (\Exception $exception) {
            return response()->error($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAwardTypeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAwardTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AwardType  $awardType
     * @return \Illuminate\Http\Response
     */
    public function show(AwardType $awardType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AwardType  $awardType
     * @return \Illuminate\Http\Response
     */
    public function edit(AwardType $awardType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAwardTypeRequest  $request
     * @param  \App\Models\AwardType  $awardType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAwardTypeRequest $request, AwardType $awardType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AwardType  $awardType
     * @return \Illuminate\Http\Response
     */
    public function destroy(AwardType $awardType)
    {
        //
    }
}
