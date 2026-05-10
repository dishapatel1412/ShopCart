<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Size;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::latest()->get();
        return view('admin.sizes.index', compact('sizes'));
    }

    public function create()
    {
        return view('admin.sizes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:sizes',
        ]);

        Size::create(['name' => $request->name]);

        return redirect()->route('admin.sizes.index')->with('success', 'Size created!');
    }

    public function edit($id)
    {
        $size = Size::findOrFail($id);
        return view('admin.sizes.edit', compact('size'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:sizes,name,' . $id,
        ]);

        Size::findOrFail($id)->update(['name' => $request->name]);

        return redirect()->route('admin.sizes.index')->with('success', 'Size updated!');
    }

    public function destroy($id)
    {
        Size::findOrFail($id)->delete();
        return redirect()->route('admin.sizes.index')->with('success', 'Size deleted!');
    }
}
