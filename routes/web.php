<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/db-info', function () {
    return response()->json([
        'domain' => request()->getHost(),
        'database_name' => DB::connection()->getDatabaseName(),
        'connection_name' => config('database.default'),
        'host' => config('database.connections.' . config('database.default') . '.host'),
    ]);
});
Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
    echo 'Application cache has been cleared';
    return redirect()->back();
});

Route::redirect('/', '/login');
require __DIR__ . '/auth.php';
