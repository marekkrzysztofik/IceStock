<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;
    protected $fillable = ['source_storage_id', 'destination_storage_id', 'ice_cream_id', 'quantity', 'status'];
    public function source() { return $this->belongsTo(Storage::class, 'source_storage_id'); }
    public function destination() { return $this->belongsTo(Storage::class, 'destination_storage_id'); }
    public function iceCream() { return $this->belongsTo(IceCream::class); }
}
