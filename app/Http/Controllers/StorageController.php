<?php

namespace App\Http\Controllers;

use App\Models\Storage;

use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function index()
    {
        return Storage::all();
    }
    
    public function store(Request $request)
    {
        return Storage::create($request->validate([
            'shop_id' => 'required|exists:shops,id',
            'name' => 'required|string|max:255'
        ]));
    }

    public function destroy($id)
    {
        Storage::findOrFail($id)->delete();
        return response()->noContent();
    }
}
