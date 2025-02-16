<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
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
    public function bulkUpdate(Request $request)
    {
        Log::info('Przetworzone dane:', ['data' => $request->all()]);

        $validated = $this->validateInventoryData($request);

        try {
            Inventory::upsert($validated, ['id'], ['quantity', 'storage_id', 'ice_cream_id']);
            return response()->json(['message' => 'Zbiorcza aktualizacja zakończona sukcesem!']);
        } catch (\Exception $e) {
            Log::error('Błąd bazy danych:', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            return response()->json(['error' => 'Błąd podczas zapisywania danych.'], 500);
        }
    }

    private function validateInventoryData(Request $request): array
    {
        return $request->validate([
            'updatedRows' => 'required|array',
            'updatedRows.*.id' => 'required|integer|exists:inventories,id',
            'updatedRows.*.storage_id' => 'required|integer|exists:storages,id',
            'updatedRows.*.ice_cream_id' => 'required|integer|exists:ice_creams,id',
            'updatedRows.*.quantity' => 'required|integer|min:0'
        ])['updatedRows'];
    }

    public function show($id)
    {
        return Inventory::with(['storage', 'iceCream'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        return tap(Inventory::findOrFail($id))->update(
            $request->validate(['quantity' => 'required|integer|min:0'])
        );
    }

    public function destroy($id)
    {
        Inventory::findOrFail($id)->delete();
        return response()->noContent();
    }
}
