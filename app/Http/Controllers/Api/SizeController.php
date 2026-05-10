<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Size;

class SizeController extends Controller
{
    public function index(Request $request)
    {
        $query = Size::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('name', 'LIKE', "%{$search}%");
        }
        $sizes = $query->paginate(10);
        return response()->json($sizes);
        // return response()->json(Size::all());
    }

    public function store(Request $request)
    {
        $size = Size::create([
            'name' => $request->name
        ]);

        return response()->json($size, 201);
    }

    public function update(Request $request, $id)
    {
        $size = Size::findOrFail($id);

        $size->update([
            'name' => $request->name
        ]);

        return response()->json($size);
    }

    public function destroy($id)
    {
        Size::findOrFail($id)->delete();

        return response()->json(['message' => 'Size deleted']);
    }
}
