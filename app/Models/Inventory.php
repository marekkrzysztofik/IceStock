<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $fillable = ['storage_id', 'ice_cream_id', 'quantity'];
    public function storage() { return $this->belongsTo(Storage::class); }
    public function iceCream() { return $this->belongsTo(IceCream::class); }
}
