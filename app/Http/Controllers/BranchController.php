<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::all();
        return view('auth.Branch', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'contact' => ['nullable', 'digits:10', 'regex:/^[6-9]\d{9}$/'],
            'branch_email' => 'nullable|email|max:255',
            'manager_email' => 'nullable|email|max:255',
        ]);

        Branch::create($request->all());

        return redirect()->route('dashboard.branches.index')->with('success', 'Branch created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $branch = Branch::findOrFail($id);
        return view('dashboard.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $branch = Branch::findOrFail($id);
        return view('dashboard.branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $branch = Branch::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'contact' => ['nullable', 'digits:10', 'regex:/^[6-9]\d{9}$/'],
            'branch_email' => 'nullable|email|max:255',
            'manager_email' => 'nullable|email|max:255',
        ]);

        $branch->update($request->all());

        return redirect()->route('dashboard.branches.index')->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();

        return redirect()->route('dashboard.branches.index')->with('success', 'Branch deleted successfully.');
    }
}