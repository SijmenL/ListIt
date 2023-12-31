<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::find($id); // Assuming you're using Eloquent

        $ownerUsername = $product->owner->username;

        return view('products.show', compact('product', 'ownerUsername'));
    }

    public function overview()
    {
        $products = Product::where('private', false)->get();
        $tags = Tag::all();
        $selectedTags = ['0'];
        $viewOwnProducts = 'true';
        $search = '';
        $totalProducts = $products->count();
        return view('products.list', compact('products', 'tags', 'selectedTags', 'viewOwnProducts', 'search', 'totalProducts'));
    }

    public function filterProducts(Request $request)
    {
        $userId = Auth::id();

        $viewOwnProducts = $request->input('own_product');
        $search = $request->input('search');
        $selectedTags = $request->input('tags');

        if ($viewOwnProducts !== 'true') {
            $viewOwnProducts = 'false';
        }

        if ($search === null) {
            $search = '';
        }

        if ($selectedTags !== null) {
            if ($viewOwnProducts !== 'true') {
                $products = Product::with('tags')
                    ->whereHas('tags', function ($query) use ($selectedTags) {
                        $query->whereIn('tags.id', $selectedTags);
                    })
                    ->where('product_owner', '!=', $userId)
                    ->where(function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%')
                            ->orWhere('subtitle', 'like', '%' . $search . '%')
                            ->orWhere('price', 'like', '%' . $search . '%');
                    })
                    ->where('private', false)
                    ->get();
            } else {
                $products = Product::with('tags') // Eager load tags
                ->whereHas('tags', function ($query) use ($selectedTags) {
                    $query->whereIn('tags.id', $selectedTags);
                })->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('subtitle', 'like', '%' . $search . '%')
                        ->orWhere('price', 'like', '%' . $search . '%');
                })
                    ->where('private', false)
                    ->get();
            }
        } else {
            if ($viewOwnProducts !== 'true') {
                $products = Product::where('product_owner', '!=', $userId)->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('subtitle', 'like', '%' . $search . '%')
                        ->orWhere('price', 'like', '%' . $search . '%');
                })
                    ->where('private', false)
                    ->get();
            } else {
                $products = Product::where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('subtitle', 'like', '%' . $search . '%')
                        ->orWhere('price', 'like', '%' . $search . '%');
                })
                    ->where('private', false)
                    ->get();
            }
            $selectedTags = ['0'];
        }

        $tags = Tag::all();

        $totalProducts = $products->count();

        return view('products.list', compact('products', 'tags', 'selectedTags', 'viewOwnProducts', 'search', 'totalProducts'));
    }

    public function addToList($id)
    {
        $product = Product::find($id); // Assuming you're using Eloquent

        $user = Auth::id();

        $lists = Catalog::where('user_id', $user)->get();

        $ownerUsername = $product->owner->username;


        return view('products.add-to-list', compact('product', 'lists', 'ownerUsername'));
    }

    public function addToListStore(Request $request)
    {
        $validatedData = $request->validate([
            'catalog' => 'required|int',
            'id' => 'required|int',
        ]);

        $catalogId = $validatedData['catalog'];
        $productId = $validatedData['id'];

        $catalog = Catalog::find($catalogId);
        $product = Product::find($productId);

        if (!$catalog || !$product) {
            return redirect()->back()->with('error', 'Invalid catalog or product ID.');
        }

        if ($catalog->products->contains($product)) {
            return redirect()->back()->with('error', 'Product is already in the list.');
        }

        $catalog->products()->attach($product);

        return redirect()->back()->with('success', 'Product added to the list.');
    }

}
