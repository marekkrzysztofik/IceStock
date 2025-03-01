<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['store_id', 'ice_cream_id', 'quantity', 'total_price'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function iceCream()
    {
        return $this->belongsTo(IceCream::class);
    }
}
