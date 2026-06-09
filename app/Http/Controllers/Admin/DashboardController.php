<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $tableCount = count(DB::select('SHOW TABLES')) - 4;
        return view('admin.dashboard', compact('admin', 'tableCount'));
    }
}
