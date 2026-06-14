<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PencarianLog;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        return view('user.user_dashboard');
    }

    public function riwayat()
    {
        $logs = PencarianLog::where('user_id', Auth::guard('users')->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('user.riwayat', compact('logs'));
    }
}
