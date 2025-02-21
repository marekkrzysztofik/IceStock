<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = ['transfer_id', 'ice_cream_id', 'quantity', 'user_id', 'batch_id'];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }
}
