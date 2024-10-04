<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function getCategoryData(Request $request)
    {
        if ($request->ajax()) {
            $categories = Category::all();
            return DataTables::of($categories)
                ->addIndexColumn()
                ->make(true);
        }
        return abort(404);
    }
    // Display a listing of the products
    public function index()
    {
        $categories = Category::all();
        return view('category.index', compact('categories'));
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    // Function to show the edit form for a specific product
    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function update(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        // Find the product by ID and update its properties
        $category = Category::findOrFail($validatedData['id']);
        $category->update($validatedData);

        // Redirect back with a success message
        return redirect()->route('category.index')->with('success', 'Category updated successfully!');
    }

    // Add the delete method for deleting a product
    public function destroy($id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->delete();
            return response()->json(['success' => 'Category deleted successfully.']);
        }

        return response()->json(['error' => 'Category not found.'], 404);
    }

    public function duplicate($id)
    {
        $category = Category::findOrFail($id);

        // Create a new product instance with the same details
        $newCategory = $category->replicate(); // Replicate the product

        // Modify the unique fields to prevent conflict
        $newCategory->name = $newCategory->name . ' - Copy'; // Modify the name to avoid duplicate entry
        $newCategory->id = null; // Reset ID for the new product


        // Validate the new product
        $validator = Validator::make($newCategory->toArray(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        // Save the new product
        $newCategory->save();

        return response()->json([
            'success' => 'Category duplicated successfully.',
            'category' => $newCategory
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create($request->all());

        return response()->json(['success' => true, 'category' => $category]);
    }
}
