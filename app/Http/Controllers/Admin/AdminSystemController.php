<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemConfig;
use Illuminate\Http\Request;

class AdminSystemController extends Controller
{
    public function index()
    {
        $configs = SystemConfig::all()->keyBy('key');
        return view('admin.system.index', compact('configs'));
    }

    public function updateFlags(Request $request)
    {
        $flags = $request->input('flags', []);
        
        $availableFlags = [
            'ai_enabled',
            'video_calls_enabled',
            'registration_open',
        ];

        foreach ($availableFlags as $flag) {
            SystemConfig::setVal("flag_{$flag}", in_array($flag, $flags), 'boolean');
        }

        SystemConfig::setVal('global_allow_marketplace', in_array('global_allow_marketplace', $flags), 'boolean');

        return back()->with('success', 'Feature flags updated successfully.');
    }

    public function toggleMaintenance(Request $request)
    {
        $maintenanceMode = $request->has('maintenance_mode');
        SystemConfig::setVal('maintenance_mode', $maintenanceMode, 'boolean');

        $message = $maintenanceMode ? 'Maintenance mode enabled. Only admins can access the platform.' : 'Maintenance mode disabled. Platform is live.';
        return back()->with('success', $message);
    }
}
