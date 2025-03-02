<?php

namespace App\Http\Controllers;

use App\Models\IceCream;
use Illuminate\Http\Request;

class IceCreamController extends Controller
{
    public function index()
    { 
        return IceCream::all()->map(function ($iceCream) {
            return [
                'id' => $iceCream->id,
                'name' => $iceCream->name,
                'quantity' => 0, // Dodajemy domyślną wartość quantity = 0
            ];
        });
    }

    public function store(Request $request)
    {
        return IceCream::create($request->validate(['name' => 'required|string|max:255']));
    }
    public function destroy($id)
    {
        IceCream::findOrFail($id)->delete();
    }
}
