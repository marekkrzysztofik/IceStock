<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IceCream extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    // Automatyczne tworzenie inventory dla nowego smaku
    protected static function booted()
    {
        static::created(function ($iceCream) {
            // Wstawienie wpisów dla nowego smaku we wszystkich magazynach
            DB::insert("
                INSERT INTO inventories (storage_id, ice_cream_id, quantity, created_at, updated_at)
                SELECT id, ?, 0, NOW(), NOW() FROM storages
            ", [$iceCream->id]);
        });
    }
}
