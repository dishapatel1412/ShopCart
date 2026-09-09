<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $products = Product::where('is_active', '1')
            ->with(['category', 'sizes', 'colors'])
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('price', 'like', "%$search%")
                    ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%$search%"));
            })
            ->paginate(8)
            ->withQueryString();

        $productsJson = $products->map(function ($p) {
            return [
                'id'       => $p->id,
                'name'     => $p->name,
                'price'    => $p->price,
                'image'    => $p->image ? asset('storage/' . $p->image) : '',
                'category' => $p->category->name ?? '',
                'sizes'    => $p->sizes->map(function ($s) {
                    return ['id' => $s->id, 'name' => $s->name];
                })->values(),
                'colors'   => $p->colors->map(function ($c) {
                    return ['id' => $c->id, 'name' => $c->name, 'hex' => $c->hex_code ?? '#ccc'];
                })->values(),
            ];
        })->values();

        // Search by name
        // if ($request->filled('search')) {
        //     $products->where('name', 'like', '%' . $request->search . '%');
        // }

        $users = User::where('id', '!=', Auth::id())->get();

        return view('dashboard', compact('products', 'search', 'productsJson', 'users'));
    }
}
