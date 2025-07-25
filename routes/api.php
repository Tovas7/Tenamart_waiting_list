<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WaitingListController;
use Illuminate\Support\Facades\Hash; // Needed for the token generation example

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication route (for generating tokens - you might want a more sophisticated login)
// For simplicity, we'll assume tokens are generated via Tinker or a separate admin panel.
// For a real app, you'd have a POST /login route.
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Waiting List API routes, protected by Sanctum authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('waiting-list', WaitingListController::class);

    // Combined stats endpoint
    Route::get('waiting-list/stats', [WaitingListController::class, 'getStats']);

    // New CSV Export endpoint
    Route::get('waiting-list/export-csv', [WaitingListController::class, 'exportCsv']); // <-- This is the added line

    // Individual Insight routes (as discussed previously - these can be kept or removed if 'stats' is comprehensive)
    Route::get('waiting-list/insights/total-signups', [WaitingListController::class, 'getTotalSignups']);
    Route::get('waiting-list/insights/signups-by-source', [WaitingListController::class, 'getSignupsBySource']);
    Route::get('waiting-list/insights/signups-by-month', [WaitingListController::class, 'getSignupsByMonth']);
});

// Optional: Route for creating an initial token for testing (remove in production)
// You would typically generate tokens through a proper user login system
Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = App\Models\User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    return response()->json(['token' => $user->createToken($request->device_name)->plainTextToken]);
});
