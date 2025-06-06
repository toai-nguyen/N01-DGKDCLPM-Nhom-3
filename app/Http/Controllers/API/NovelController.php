<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Novel;
use App\Http\Resources\NovelResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NovelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): NovelResource
    {
        //find one novel and return it
        // Log::info('NovelController@index');
        return new NovelResource(Novel::find(1));
        // return NovelResource::collection(Novel::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
