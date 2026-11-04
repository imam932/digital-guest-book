<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\GuestBook;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_entries' => GuestBook::count(),
            'total_companies' => Company::count(),
            'pending_messages' => GuestBook::where('display_status', 'pending')->count(),
            'displayed_messages' => GuestBook::where('display_status', 'displayed')->count(),
        ];

        $latestMessages = GuestBook::query()
            ->with('company:id,name')
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'latestMessages'));
    }
}
