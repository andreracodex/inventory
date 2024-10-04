<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UnitsController extends Controller
{
    public function getUnitsData(Request $request)
    {
        if ($request->ajax()) {
            $units = Unit::all();
            return DataTables::of($units)
                ->addIndexColumn()
                ->make(true);
        }
        return abort(404);
    }
    // Display a listing of the products
    public function index()
    {
        $units = Unit::all();
        return view('units.index', compact('units'));
    }

    public function show($id)
    {
        $units = Unit::findOrFail($id);
        return response()->json($units);
    }

    // Function to show the edit form for a specific product
    public function edit($id)
    {
        $units = Unit::findOrFail($id);

        return response()->json([
            'units' => $units,
        ]);
    }

    public function update(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
        ]);

        // Find the product by ID and update its properties
        $units = Unit::findOrFail($validatedData['id']);
        $units->update($validatedData);

        // Redirect back with a success message
        return redirect()->route('category.index')->with('success', 'Units updated successfully!');
    }

    // Add the delete method for deleting a product
    public function destroy($id)
    {
        $units = Unit::find($id);

        if ($units) {
            $units->delete();
            return response()->json(['success' => 'Units deleted successfully.']);
        }

        return response()->json(['error' => 'Units not found.'], 404);
    }

    public function duplicate($id)
    {
        $units = Unit::findOrFail($id);

        // Create a new product instance with the same details
        $newUnits = $units->replicate(); // Replicate the product

        // Modify the unique fields to prevent conflict
        $newUnits->name = $newUnits->name . ' - Copy'; // Modify the name to avoid duplicate entry
        $newUnits->id = null; // Reset ID for the new product


        // Validate the new product
        $validator = Validator::make($newUnits->toArray(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        // Save the new product
        $newUnits->save();

        return response()->json([
            'success' => 'Units duplicated successfully.',
            'category' => $newUnits
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $units = Unit::create($request->all());

        return response()->json(['success' => true, 'units' => $units]);
    }
}
