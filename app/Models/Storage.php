<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    use HasFactory;
    protected $fillable = ['shop_id', 'name'];
    public function Shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function inventories()
{
    return $this->hasMany(Inventory::class);
}
}
