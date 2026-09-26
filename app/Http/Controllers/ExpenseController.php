<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Branch; // 🟢 Branch Model import kele aahe
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    // All Expenses page दाखवण्यासाठी
    public function index(Request $request)
    {
        $query = Expense::with('category');

        // Search feature (category name ya description nusar)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('category', function($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                })->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $expenses = $query->latest()->paginate(10)->withQueryString();
        
        // Total expenses calculation (फिल्टर्ड किंवा सर्च केलेल्या रकमांची बेरीज)
        $totalExpenses = (clone $query)->sum('amount');

        // Filter dropdown sathi categories fetchelya
        $categories = ExpenseCategory::where('status', 'Active')->get();

        return view('admin.expenses.index', compact('expenses', 'totalExpenses', 'categories'));
    }

    // Add Expense फॉर्म दाखवण्यासाठी नवीन मेथड
    public function create()
    {
        $categories = ExpenseCategory::where('status', 'Active')->get();
        
        return view('admin.expenses.create', compact('categories'));
    }

    // नवीन एक्सपेंस डेटाबेसमध्ये सेव्ह करण्यासाठी नवीन मेथड
    public function store(Request $request)
    {
        $request->validate([
            'category_id'    => 'required|exists:expense_categories,id',
            'amount'         => 'required|numeric|gt:0', // अमाऊंट 0 पेक्षा जास्त आणि निगेटिव्ह नसावी
            'payment_method' => 'required|string',
            'expense_date'   => 'required|date',
            'description'    => 'nullable|string',
            'attachment'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // जास्तीत जास्त 2MB फाईल
        ]);

        $filePath = null;

        // जर फाईल अपलोड केली असेल तर स्टोरेजमध्ये सेव्ह करा
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('expenses_attachments', $filename, 'public');
        }

        // डेटाबेसमध्ये रेकॉर्ड सेव्ह करा
        Expense::create([
            'category_id'    => $request->category_id,
            'category'       => $request->category_id, // 🟢 जुन्या database column चा error टाळण्यासाठी
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'expense_date'   => $request->expense_date,
            'description'    => $request->description,
            'attachment'     => $filePath,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully!');
    }

    // ==========================================
    // 🟢 नवीन ॲड केलेल्या मेथड्स (Edit, Update, Destroy, Categories)
    // ==========================================

    // एक्सपेंस एडिट फॉर्म दाखवण्यासाठी
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        
        $categories = ExpenseCategory::where('status', 'Active')->get();

        return view('admin.expenses.edit', compact('expense', 'categories'));
    }

    // एक्झिस्टिंग एक्सपेंस अपडेट करण्यासाठी
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $request->validate([
            'category_id'    => 'required|exists:expense_categories,id',
            'amount'         => 'required|numeric|gt:0',
            'payment_method' => 'required|string',
            'expense_date'   => 'required|date',
            'description'    => 'nullable|string',
            'attachment'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $filePath = $expense->attachment;

        // जर नवीन फाईल अपलोड केली असेल तर जुनी डिलीट करून नवीन सेव्ह करा
        if ($request->hasFile('attachment')) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('expenses_attachments', $filename, 'public');
        }

        $expense->update([
            'category_id'    => $request->category_id,
            'category'       => $request->category_id, // 🟢 जुन्या database column चा error टाळण्यासाठी
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'expense_date'   => $request->expense_date,
            'description'    => $request->description,
            'attachment'     => $filePath,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully!');
    }

    // एक्सपेंस डिलीट करण्यासाठी
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        // अटॅचमेंट फाईल असेल तर ती स्टोरेजमधून डिलीट करा
        if ($expense->attachment && Storage::disk('public')->exists($expense->attachment)) {
            Storage::disk('public')->delete($expense->attachment);
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully!');
    }

    // एक्सपेंस कॅटेगरीजची लिस्ट पाहण्यासाठी (साथीला branches पण पास केल्या आहेत)
    public function categories(Request $request)
    {
        $query = ExpenseCategory::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->latest()->paginate(10);
        $branches = Branch::all(); // 🟢 Modal मध्ये branch select करण्यासाठी लागतात

        return view('admin.expenses.categories', compact('categories', 'branches'));
    }

    // नवीन कॅटेगरी सेव्ह करण्यासाठी
    public function storeCategory(Request $request)
    {
        $request->validate([
            'branch_id'   => 'nullable|exists:branches,id', // 🟢 Branch validation
            'name'        => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string',
        ]);

        ExpenseCategory::create([
            'branch_id'   => $request->branch_id, // 🟢 Branch ID save kela
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => 'Active',
        ]);

        return redirect()->route('expenses.categories.index')->with('success', 'Expense category added successfully!');
    }

    // कॅटेगरी अपडेट करण्यासाठी
    public function updateCategory(Request $request, $id)
    {
        $category = ExpenseCategory::findOrFail($id);

        $request->validate([
            'branch_id'   => 'nullable|exists:branches,id',
            'name'        => 'required|string|max:255|unique:expense_categories,name,' . $id,
            'description' => 'nullable|string',
            'status'      => 'required|in:Active,Inactive',
        ]);

        $category->update([
            'branch_id'   => $request->branch_id,
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
        ]);

        return redirect()->route('expenses.categories.index')->with('success', 'Expense category updated successfully!');
    }

    // कॅटेगरी डिलीट/डिअॅक्टिव्हेट करण्यासाठी
    public function destroyCategory($id)
    {
        $category = ExpenseCategory::findOrFail($id);

        if ($category->expenses()->count() > 0) {
            $category->update(['status' => 'Inactive']);
            return redirect()->route('expenses.categories.index')->with('success', 'Category is in use by expenses, so it has been marked Inactive safely!');
        }

        $category->delete();
        return redirect()->route('expenses.categories.index')->with('success', 'Category deleted successfully!');
    }
}