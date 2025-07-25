<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WaitingList;
use Illuminate\Http\Request;
use App\Http\Resources\WaitingListResource;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; // Import Carbon for date manipulation
use Illuminate\Support\Facades\DB; // Import DB facade for raw queries

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

    /**
     * Get various statistics for the waiting list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStats()
    {
        // 1. Total number of signups
        $totalSignups = WaitingList::count();

        // 2. Number of signups per source
        $signupsBySource = WaitingList::select('signup_source', DB::raw('count(*) as count'))
            ->groupBy('signup_source')
            ->get();

        // 3. Daily signup trends (last 30 days)
        // Set the start date for the last 30 days
        $last30Days = Carbon::now()->subDays(30)->startOfDay();

        $dailySignupsRaw = WaitingList::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', $last30Days)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->count];
            });

        // Create a complete date range for the last 30 days
        $dateRange = new \DatePeriod(
            $last30Days,
            new \DateInterval('P1D'),
            Carbon::now()->addDay()->startOfDay() // Go up to tomorrow's start to include today
        );

        $dailyTrend = collect();
        foreach ($dateRange as $date) {
            $formattedDate = $date->format('Y-m-d');
            $dailyTrend->put($formattedDate, $dailySignupsRaw->get($formattedDate, 0));
        }

        // 4. Peak signup days
        // Get the day(s) with the highest number of signups in the last 30 days
        // We use the $dailySignupsRaw collection for this, as it contains only the days with actual signups.
        $peakSignups = $dailySignupsRaw->sortDesc()->take(5); // Get top 5 peak days (date => count)
        // If you only wanted the single peak day:
        // $singlePeakDay = $dailySignupsRaw->sortByDesc('count')->first();


        return response()->json([
            'total_signups' => $totalSignups,
            'signups_by_source' => $signupsBySource,
            'daily_signup_trends_last_30_days' => $dailyTrend,
            'peak_signup_days' => $peakSignups,
        ]);
    }

    // Optional: If you want to keep these separate insight methods in addition to getStats
    /**
     * Get total number of signups.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotalSignups()
    {
        $total = WaitingList::count();
        return response()->json(['total_signups' => $total]);
    }

    /**
     * Get signups grouped by source.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSignupsBySource()
    {
        $signupsBySource = WaitingList::select('signup_source', DB::raw('count(*) as count'))
                                     ->groupBy('signup_source')
                                     ->get();
        return response()->json($signupsBySource);
    }

    /**
     * Get signups grouped by month.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSignupsByMonth()
    {
        $signupsByMonth = WaitingList::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
                                     ->groupBy('month')
                                     ->orderBy('month')
                                     ->get();
        return response()->json($signupsByMonth);
    }
}
