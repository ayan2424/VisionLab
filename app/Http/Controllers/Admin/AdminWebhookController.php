<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminWebhookController extends Controller
{
    public function index()
    {
        $webhooks = Webhook::latest()->get();
        return Inertia::render('Admin/Webhooks/Index', compact('webhooks'));
    }

    public function create()
    {
        return Inertia::render('Admin/Webhooks/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url|max:255',
            'events' => 'required|array',
            'events.*' => 'string',
            'secret' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Webhook::create($validated);

        return redirect()->route('admin.webhooks.index')->with('success', 'Webhook created successfully.');
    }

    public function edit(Webhook $webhook)
    {
        return Inertia::render('Admin/Webhooks/Form', compact('webhook'));
    }

    public function update(Request $request, Webhook $webhook)
    {
        $validated = $request->validate([
            'url' => 'required|url|max:255',
            'events' => 'required|array',
            'events.*' => 'string',
            'secret' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $webhook->update($validated);

        return redirect()->route('admin.webhooks.index')->with('success', 'Webhook updated successfully.');
    }

    public function destroy(Webhook $webhook)
    {
        $webhook->delete();
        return redirect()->route('admin.webhooks.index')->with('success', 'Webhook deleted successfully.');
    }
}
