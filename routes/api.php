<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IceCreamController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\ProductionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('icecreams', IceCreamController::class);

Route::apiResource('shops', ShopController::class);

Route::apiResource('inventories', InventoryController::class);
Route::post('inventories/bulk-update', [InventoryController::class, 'bulkUpdate']);

Route::post('transfers', [TransferController::class, 'createTransfer']);

Route::apiResource('storages', StorageController::class);

Route::apiResource('productions', ProductionController::class);