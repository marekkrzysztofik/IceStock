<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transfer;
use App\Models\Inventory;
use App\Models\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Production;

class TransferController extends Controller
{

    public function index()
    {
        // Pobranie transferów
        $transfers = Transfer::with(['source', 'destination', 'iceCream'])
            ->get()
            ->map(function ($transfer) {
                return [
                    'id' => $transfer->id,
                    'type' => $transfer->status === 'production' ? 'Production' : 'Transfer',
                    'source' => $transfer->source_storage_id ? $transfer->source->name : 'Produkcja ' . $transfer->destination->Shop->name,
                    'destination' => $transfer->destination->name,
                    'ice_cream' => $transfer->iceCream->name,
                    'quantity' => $transfer->quantity,
                    'date' => $transfer->created_at->format('d-m-Y H:i'),
                    'status' => ucfirst($transfer->status),
                ];
            });
        return $transfers;
    }


    public function createTransfer(Request $request)
    {
        $validated = $request->validate([
            'source_storage_id' => 'required|exists:storages,id',
            'destination_storage_id' => 'required|exists:storages,id|different:source_storage_id',
            'ice_cream_id' => 'required|exists:ice_creams,id',
            'quantity' => 'required|integer|min:1',
        ]);
        Log::info("Szukam inventory dla", [
            'storage_id' => $validated['source_storage_id'],
            'ice_cream_id' => $validated['ice_cream_id']
        ]);
        $inventory = Inventory::where('storage_id', $validated['source_storage_id'])
            ->where('ice_cream_id', $validated['ice_cream_id'])
            ->first();
        if (!$inventory) {
            Log::error("Nie znaleziono inventory!", [
                'storage_id' => $validated['source_storage_id'],
                'ice_cream_id' => $validated['ice_cream_id']
            ]);
            return response()->json(['error' => 'Brak produktu w magazynie'], 404);
        }
        Log::info("Znaleziono inventory:", ['quantity' => $inventory->quantity]);

        if (!$inventory || $inventory->quantity < $validated['quantity']) {
            return response()->json(['error' => 'Niewystarczająca ilość produktu w magazynie'], 400);
        }

        $sourceStorage = Storage::find($validated['source_storage_id']);
        $destinationStorage = Storage::find($validated['destination_storage_id']);

        $status = $sourceStorage->shop_id === $destinationStorage->shop_id ? 'zatwierdzony' : 'oczekujacy';

        DB::transaction(function () use ($validated, $inventory, $status) {
            Transfer::create(array_merge($validated, ['status' => $status]));

            $inventory->decrement('quantity', $validated['quantity']);

            if ($status === 'zatwierdzony') {
                Inventory::updateOrCreate(
                    [
                        'storage_id' => $validated['destination_storage_id'],
                        'ice_cream_id' => $validated['ice_cream_id'],
                    ],
                    ['quantity' => DB::raw("quantity + {$validated['quantity']}")]
                );
            }
        });

        return response()->json(['message' => "Transfer utworzony pomyślnie" . ($status === 'oczekujacy' ? " i oczekuje na zatwierdzenie" : " i został automatycznie zatwierdzony")]);
    }

    public function approveTransfer(Transfer $transfer)
    {
        if ($transfer->status !== 'oczekujacy') {
            return response()->json(['error' => 'Transfer został już zatwierdzony'], 400);
        }

        DB::transaction(function () use ($transfer) {
            $destinationInventory = Inventory::firstOrNew([
                'storage_id' => $transfer->destination_storage_id,
                'ice_cream_id' => $transfer->ice_cream_id,
            ]);

            $destinationInventory->quantity += $transfer->quantity;
            $destinationInventory->save();

            $transfer->status = 'zatwierdzony';
            $transfer->save();
        });

        return response()->json(['message' => 'Transfer zatwierdzony pomyślnie']);
    }
}
