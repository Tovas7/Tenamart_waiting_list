<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WaitingList;
use Illuminate\Http\Request;
use App\Http\Resources\WaitingListResource;
use Illuminate\Support\Facades\Validator;

class WaitingListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = WaitingList::query();

        // Optional: Filter by source
        if ($request->has('source')) {
            $query->where('signup_source', $request->source);
        }

        // Optional: Filter by date (e.g., created_at from/to)
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $signups = $query->paginate(10); // Paginate with 10 items per page

        return WaitingListResource::collection($signups);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:waiting_list,email',
            'signup_source' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422); // Unprocessable Entity
        }

        $signup = WaitingList::create($request->all());

        return new WaitingListResource($signup);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WaitingList  $waitingList
     * @return \Illuminate\Http\Response
     */
    public function show(WaitingList $waitingList)
    {
        return new WaitingListResource($waitingList);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WaitingList  $waitingList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WaitingList $waitingList)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:waiting_list,email,' . $waitingList->id, // Exclude current ID
            'signup_source' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $waitingList->update($request->all());

        return new WaitingListResource($waitingList);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WaitingList  $waitingList
     * @return \Illuminate\Http\Response
     */
    public function destroy(WaitingList $waitingList)
    {
        $waitingList->delete();

        return response()->json(null, 204); // No Content
    }
}