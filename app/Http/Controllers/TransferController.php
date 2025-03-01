<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transfer;
use App\Models\Inventory;
use App\Models\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                    'source' => $transfer->source_storage_id,
                    'destination' => $transfer->destination->name,
                    'ice_cream' => $transfer->iceCream->name,
                    'quantity' => $transfer->quantity,
                    'date' => $transfer->created_at->format('d-m-Y H:i'),
                    'status' => $transfer->status,
                ];
            });
        return $transfers;
    }


    public function createTransfer(Request $request)
    {
        $validated = $this->validateTransferRequest($request);

        $status = $this->determineTransferStatus(
            $validated['source_storage_id'],
            $validated['destination_storage_id']
        );

        return DB::transaction(function () use ($validated, $status) {
            $this->checkInventory($validated);

            $this->processTransfers($validated, $status);

            $this->updateSourceInventory($validated);

            if ($status === 'zatwierdzony') {
                $this->updateDestinationInventory($validated);
            }

            return response()->json([
                'message' => "Transfer utworzony pomyślnie" .
                    ($status === 'oczekujacy' ? " i oczekuje na zatwierdzenie" : " i został automatycznie zatwierdzony"),
            ]);
        });
    }

    private function validateTransferRequest($request)
    {
        return $request->validate([
            'source_storage_id' => 'required|exists:storages,id',
            'destination_storage_id' => 'required|exists:storages,id|different:source_storage_id',
            'ice_creams' => 'required|array|min:1',
            'ice_creams.*.ice_cream_id' => 'required|exists:ice_creams,id',
            'ice_creams.*.quantity' => 'required|integer|min:1',
        ]);
    }

    private function determineTransferStatus($sourceStorageId, $destinationStorageId)
    {
        return Storage::where('id', $sourceStorageId)->value('shop_id') ===
            Storage::where('id', $destinationStorageId)->value('shop_id')
            ? 'zatwierdzony' : 'oczekujacy';
    }

    private function checkInventory($validated)
    {
        $inventories = Inventory::where('storage_id', $validated['source_storage_id'])
            ->whereIn('ice_cream_id', array_column($validated['ice_creams'], 'ice_cream_id'))
            ->get()
            ->keyBy('ice_cream_id');

        foreach ($validated['ice_creams'] as $iceCream) {
            if (
                !isset($inventories[$iceCream['ice_cream_id']]) ||
                $inventories[$iceCream['ice_cream_id']]->quantity < $iceCream['quantity']
            ) {
                throw new \Exception("Brak wystarczającej ilości dla smaku ID: {$iceCream['ice_cream_id']}");
            }
        }
    }

    private function processTransfers($validated, $status)
    {
        Transfer::insert(array_map(fn($iceCream) => [
            'source_storage_id' => $validated['source_storage_id'],
            'destination_storage_id' => $validated['destination_storage_id'],
            'ice_cream_id' => $iceCream['ice_cream_id'],
            'quantity' => $iceCream['quantity'],
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now()
        ], $validated['ice_creams']));
    }

    private function updateSourceInventory($validated)
    {
        foreach ($validated['ice_creams'] as $iceCream) {
            Inventory::where('storage_id', $validated['source_storage_id'])
                ->where('ice_cream_id', $iceCream['ice_cream_id'])
                ->decrement('quantity', $iceCream['quantity']);
        }
    }

    private function updateDestinationInventory($validated)
    {
        foreach ($validated['ice_creams'] as $iceCream) {
            Inventory::updateOrCreate(
                [
                    'storage_id' => $validated['destination_storage_id'],
                    'ice_cream_id' => $iceCream['ice_cream_id'],
                ],
                ['quantity' => DB::raw("quantity + {$iceCream['quantity']}"), 'updated_at' => now()]
            );
        }
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
