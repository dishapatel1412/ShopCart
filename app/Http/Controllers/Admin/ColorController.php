<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::latest()->get();
        return view('admin.colors.index', compact('colors'));
    }

    public function create()
    {
        return view('admin.colors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:50|unique:colors',
            'hex_code' => 'nullable|string|max:7',
        ]);

        Color::create([
            'name'     => $request->name,
            'hex_code' => $request->hex_code,
        ]);

        return redirect()->route('admin.colors.index')->with('success', 'Color created!');
    }

    public function edit($id)
    {
        $color = Color::findOrFail($id);
        return view('admin.colors.edit', compact('color'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:50|unique:colors,name,' . $id,
            'hex_code' => 'nullable|string|max:7',
        ]);

        Color::findOrFail($id)->update([
            'name'     => $request->name,
            'hex_code' => $request->hex_code,
        ]);

        return redirect()->route('admin.colors.index')->with('success', 'Color updated!');
    }

    public function destroy($id)
    {
        Color::findOrFail($id)->delete();
        return redirect()->route('admin.colors.index')->with('success', 'Color deleted!');
    }
}
