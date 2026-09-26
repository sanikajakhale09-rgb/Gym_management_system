<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Branch;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    // Inventory List & Search
    public function index(Request $request)
    {
        $query = Equipment::with('branch');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
        }

        $equipments = $query->latest()->paginate(10);
        $branches = Branch::all(); 

        return view('admin.Equipment.index', compact('equipments', 'branches'));
    }

    // Show Create Form for New Equipment
    public function create()
    {
        $branches = Branch::all();
        return view('admin.Equipment.create', compact('branches'));
    }

    // Store New Equipment
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'serial_number' => 'nullable|string|unique:equipments,serial_number',
            'purchase_price' => 'nullable|numeric',
        ]);

        Equipment::create($request->all());

        return redirect()->route('equipments.index')->with('success', 'Equipment added successfully!');
    }

    // Update Equipment
    public function update(Request $request, Equipment $equipment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'serial_number' => 'nullable|string|unique:equipments,serial_number,' . $equipment->id,
            'purchase_price' => 'nullable|numeric',
        ]);

        $equipment->update($request->all());

        return redirect()->route('equipments.index')->with('success', 'Equipment updated successfully!');
    }

    // Delete Equipment
    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('equipments.index')->with('success', 'Equipment deleted successfully!');
    }
}