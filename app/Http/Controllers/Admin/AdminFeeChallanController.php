<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeChallan;
use Illuminate\Http\Request;

class AdminFeeChallanController extends Controller
{
    public function index()
    {
        $challans = FeeChallan::with('user')->latest()->get();
        return view('admin.erp.challans.index', compact('challans'));
    }

    public function show(FeeChallan $challan)
    {
        return view('admin.erp.challans.show', compact('challan'));
    }

    public function create()
    {
        return view('admin.erp.challans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'challan_number' => 'required|string|unique:fee_challans',
            'amount' => 'required|numeric',
            'late_fee' => 'nullable|numeric',
            'due_date' => 'required|date',
        ]);

        FeeChallan::create($validated);
        return redirect()->route('admin.challans.index')->with('success', 'Fee Challan created successfully.');
    }
}
