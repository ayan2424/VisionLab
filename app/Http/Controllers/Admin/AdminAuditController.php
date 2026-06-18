<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminAuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('actor');

        if ($request->has('action') && $request->action !== '') {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->has('user_id') && $request->user_id !== '') {
            $query->where('user_id', $request->user_id);
        }

        $audits = $query->latest()->paginate(50);

        return view('admin.audits.index', compact('audits'));
    }
}
