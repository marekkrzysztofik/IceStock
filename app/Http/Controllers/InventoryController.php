<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        return Inventory::with(['storage', 'iceCream'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'storage_id' => 'required|exists:storages,id',
            'ice_cream_id' => 'required|exists:ice_creams,id',
            'quantity' => 'required|integer|min:0'
        ]);

        return Inventory::create($validated);
    }

    public function show($id)
    {
        return Inventory::with(['storage', 'iceCream'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        $inventory = Inventory::findOrFail($id);
        $inventory->update($validated);

        return $inventory;
    }

    public function destroy($id)
    {
        Inventory::findOrFail($id)->delete();
        return response()->noContent();
    }
}
