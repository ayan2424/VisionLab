<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeChallan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminFeeChallanController extends Controller
{
    public function index()
    {
        $challans = FeeChallan::with('user')->latest()->get();
        $rows = $challans->map(fn ($c) => [
            'id' => $c->id, 'challan_number' => $c->challan_number, 'student' => $c->user?->name ?? '—',
            'amount' => number_format((float) $c->amount), 'due_date' => substr((string) $c->due_date, 0, 10),
            'status' => $c->status ?? 'unpaid',
        ])->values()->all();
        return Inertia::render('Admin/Erp/ResourceIndex', [
            'title' => 'Fee Challans',
            'subtitle' => 'Issue and inspect student tuition & lab fee vouchers.',
            'basePath' => '/admin/challans',
            'createLabel' => 'Issue Challan',
            'canEdit' => false,
            'canView' => true,
            'canDelete' => false,
            'columns' => [
                ['key' => 'challan_number', 'label' => 'Voucher #', 'mono' => true],
                ['key' => 'student', 'label' => 'Student'],
                ['key' => 'amount', 'label' => 'Amount', 'mono' => true],
                ['key' => 'due_date', 'label' => 'Due Date', 'mono' => true],
                ['key' => 'status', 'label' => 'Status', 'badge' => true],
            ],
            'rows' => $rows,
        ]);
    }

    public function show(FeeChallan $challan)
    {
        $challan->load('user');
        return Inertia::render('Admin/Erp/ChallanShow', compact('challan'));
    }

    public function create()
    {
        $students = \App\Models\User::where('role', 'student')->orderBy('name')->get(['id', 'name', 'student_id']);
        return Inertia::render('Admin/Erp/ResourceForm', [
            'title' => 'Issue Fee Challan',
            'subtitle' => 'Generate a new tuition or laboratory fee voucher.',
            'submitUrl' => '/admin/challans',
            'method' => 'post',
            'cancelUrl' => '/admin/challans',
            'fields' => [
                ['name' => 'user_id', 'label' => 'Student', 'type' => 'select', 'required' => true,
                 'options' => $students->map(fn ($s) => ['value' => $s->id, 'label' => "{$s->name} (" . ($s->student_id ?? 'ID') . ")"])->values()->all()],
                ['name' => 'challan_number', 'label' => 'Challan Number', 'type' => 'text', 'required' => true, 'placeholder' => 'CH-2026-0001'],
                ['name' => 'amount', 'label' => 'Amount', 'type' => 'number', 'required' => true],
                ['name' => 'late_fee', 'label' => 'Late Surcharge', 'type' => 'number'],
                ['name' => 'due_date', 'label' => 'Due Date', 'type' => 'date', 'required' => true],
            ],
            'initialValues' => ['user_id' => '', 'challan_number' => '', 'amount' => 0, 'late_fee' => 0, 'due_date' => ''],
        ]);
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
