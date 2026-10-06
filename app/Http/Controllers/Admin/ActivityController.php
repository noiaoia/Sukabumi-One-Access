<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // <--- TAMBAHKAN BARIS INI

class ActivityController extends Controller
{
    private function validatePortal($portal_code) {
        if ($portal_code !== 'portal-' . Auth::user()->username) {
            abort(404);
        }
    }

    public function index(Request $request, $portal_code)
    {
        $this->validatePortal($portal_code);

        // Tangkap input tanggal
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $logs = ActivityLog::whereHas('user', function($query) {
            $query->where('instansi', Auth::user()->instansi);
        })
        ->when($startDate, function($q) use ($startDate) {
            return $q->whereDate('created_at', '>=', $startDate);
        })
        ->when($endDate, function($q) use ($endDate) {
            return $q->whereDate('created_at', '<=', $endDate);
        })
        ->with('user')
        ->latest()
        ->paginate(3)
        ->withQueryString(); 

        return view('pages.admin.activity.index', compact('logs', 'portal_code', 'startDate', 'endDate'));
    }
}