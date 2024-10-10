<?php

namespace App\Http\Controllers;

use App\filters\ProductFilter;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(ProductFilter $filter){
        $products = Product::filter($filter)->paginate(10);
        return api()->data('products', $products)->build();
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Save the image
        $imagePath = $request->file('image')->store('products', 'public');

        // Create a new product
        $product = Product::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
        ]);
        return api()->success(true)->data('product' , $product)->build("Individual Client Successfully Added");
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json([
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => 7,
            'orders_count' => 3,
            'revenue' => 4,
            'created_at' => $product->created_at,
            'image_url' => asset('storage/' . $product->image),
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::delete('public/' . $product->image);
        }

        $product->delete();

        return api()->success(true)->data('product' , $product)->build("Product deleted successfully");
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Update product details
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category = $request->input('category');

        // Handle image upload if present
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        return api()->success(true)->data('product' , $product)->build("Product update successfully");
    }

    public function customerProducts()
    {
        $products = Product::all()->map(function ($product) {
            $product->image = asset('storage/images/' . $product->image);
            return $product;
        });

        return api()->data('products', $products)->build();
    }

}
