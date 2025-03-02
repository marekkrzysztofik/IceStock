<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = ['shop_id', 'ice_cream_id', 'quantity', 'user_id', 'destination_storage_id'];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function iceCream()
    {
        return $this->belongsTo(IceCream::class);
    }
}
