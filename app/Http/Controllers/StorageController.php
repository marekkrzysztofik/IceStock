<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function index()
    {
        return Storage::all();
    }
    public function show($shopId)
    {
        $storages = Storage::where('shop_id', $shopId)
        ->with(['inventories.iceCream'])
        ->get()
        ->map(function ($storage) {
            return [
                'storage_id' => $storage->id,
                'storage_name' => $storage->name,
                'inventory' => $storage->inventories->map(function ($inventory) {
                    return [
                        'id' => $inventory->id,
                        'storage_id' => $inventory->storage_id,
                        'ice_cream_id' => $inventory->ice_cream_id,
                        'ice_cream_name' => $inventory->iceCream->name ?? null,
                        'quantity' => $inventory->quantity,
                    ];
                })->toArray()
            ];
        });

    return $storages;
    }
    
    public function store(Request $request)
    {
        return Storage::create($request->validate([
            'shop_id' => 'required|exists:shops,id',
            'name' => 'required|string|max:255'
        ]));
    }

    public function destroy($id)
    {
        Storage::findOrFail($id)->delete();
        return response()->noContent();
    }
}
