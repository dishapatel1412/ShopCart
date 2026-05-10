<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        $query = Color::query();

        if ($request->filled('search')) {
            $search = $request->search();

            $query->where('name', 'LIKE', "%{$search}%");
        }
        $colors = $query->paginate(10);
        return response()->json($colors);
        // return response()->json(Color::all());
    }

    public function store(Request $request)
    {
        $color = Color::create([
            'name' => $request->name,
            'hex_code' => $request->hex_code,
        ]);

        return response()->json($color, 201);
    }

    public function update(Request $request, $id)
    {
        $color = Color::findOrFail($id);

        $color->update([
            'name' => $request->name,
            'hex_code' => $request->hex_code
        ]);

        return response()->json($color);
    }

    public function destroy($id)
    {
        Color::findOrFail($id)->delete();

        return response()->json(['message' => 'Color deleted']);
    }
}
