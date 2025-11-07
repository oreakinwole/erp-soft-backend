<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AllowanceType;

class AllowanceTypeController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => AllowanceType::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:allowance_types,name',
        ]);

        $type = AllowanceType::create([
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'data' => $type,
        ], 201);
    }

    public function destroy($id)
    {
        $type = AllowanceType::findOrFail($id);
        $type->delete();
        return response()->json(['success' => true]);
    }
}