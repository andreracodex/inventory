<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function getProductsData(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::select('products.*')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->addSelect('categories.name as category_name', 'units.name as unit_name');

            return DataTables::of($products)
                ->addIndexColumn()
                ->editColumn('category_name', function ($product) {
                    return $product->category_name ?: 'N/A';
                })
                ->editColumn('unit_name', function ($product) {
                    return $product->unit_name ?: 'N/A';
                })
                ->filterColumn('category_name', function ($query, $keyword) {
                    $query->where('categories.name', 'LIKE', "%{$keyword}%");
                })
                ->filterColumn('unit_name', function ($query, $keyword) {
                    $query->where('units.name', 'LIKE', "%{$keyword}%");
                })
                ->make(true);
        }
        return abort(404);
    }
    // Display a listing of the products
    public function index()
    {
        $categories = Category::all();
        $units = Unit::all();
        $products = Product::with('category', 'unit')->get();
        return view('products.index', compact('products', 'categories', 'units'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'unit'])->findOrFail($id);
        return response()->json($product);
    }

    // Function to show the edit form for a specific product
    public function edit($id)
    {
        $units = Unit::all();
        $categories = Category::all();
        $product = Product::with(['category', 'unit'])->findOrFail($id);

        return response()->json([
            'product' => $product,
            'categories' => $categories,
            'units' => $units
        ]);
    }

    public function update(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'minimal_stock' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        // Find the product by ID and update its properties
        $product = Product::findOrFail($validatedData['id']);
        $product->update($validatedData);

        // Redirect back with a success message
        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    // Add the delete method for deleting a product
    public function destroy($id)
    {
        $product = Product::find($id);

        if ($product) {
            $product->delete();
            return response()->json(['success' => 'Product deleted successfully.']);
        }

        return response()->json(['error' => 'Product not found.'], 404);
    }

    public function duplicate($id)
    {
        $product = Product::findOrFail($id);

        // Create a new product instance with the same details
        $newProduct = $product->replicate(); // Replicate the product

        // Modify the unique fields to prevent conflict
        $newProduct->name = $newProduct->name . ' - Copy'; // Modify the name to avoid duplicate entry
        $newProduct->id = null; // Reset ID for the new product


        // Validate the new product
        $validator = Validator::make($newProduct->toArray(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'minimal_stock' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        // Save the new product
        $newProduct->save();

        return response()->json([
            'success' => 'Product duplicated successfully.',
            'product' => $newProduct
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'minimal_stock' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::create($request->all());

        return response()->json(['success' => true, 'product' => $product]);
    }

    public function consume(Request $request)
    {
        // Get the search term from the request
        $searchTerm = $request->input('search', '');
        // Retrieve products where stock is greater than zero
        $products = Product::where('stock', '>', 1)
            ->where('is_active', 1)
            ->where('name', 'like', "%{$searchTerm}%")
            ->limit(12)
            ->get();
        return view('products.consume', compact('products', 'searchTerm'));
    }
}
