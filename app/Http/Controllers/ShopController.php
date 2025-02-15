<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        return Shop::with('storages')->get();
    }
    public function show($shopId)
    {
        return Shop::with('storages')->findOrFail($shopId);
    }
}

