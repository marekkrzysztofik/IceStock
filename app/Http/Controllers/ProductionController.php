<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function store(Request $request)
    { 
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'destination_storage_id' => 'required|exists:storages,id',
            'ice_creams' => 'required|array|min:1',
            'ice_creams.*.ice_cream_id' => 'required|exists:ice_creams,id',
            'ice_creams.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            $productions = [];

            foreach ($validated['ice_creams'] as $iceCream) {
                // Tworzymy wpis w tabeli productions
                $production = Production::create([
                    'shop_id' => $validated['shop_id'],
                    'destination_storage_id' => $validated['destination_storage_id'],
                    'ice_cream_id' => $iceCream['ice_cream_id'],
                    'quantity' => $iceCream['quantity'],
                ]);

                // Aktualizacja inventory w magazynie docelowym
                Inventory::updateOrInsert(
                    ['storage_id' => $validated['destination_storage_id'], 'ice_cream_id' => $iceCream['ice_cream_id']],
                    ['quantity' => DB::raw('quantity + ' . $iceCream['quantity']), 'updated_at' => now()]
                );

                $productions[] = $production;
            }

            return response()->json([
                'message' => 'Produkcja zarejestrowana',
                'productions' => $productions
            ], 200);
        });
    }
}
