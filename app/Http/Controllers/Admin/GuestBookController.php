<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GuestBookIndexRequest;
use App\Http\Requests\Admin\UpdateGuestBookStatusRequest;
use App\Models\Company;
use App\Models\GuestBook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GuestBookController extends Controller
{
    public function index(GuestBookIndexRequest $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $companyId = $request->query('company_id');
        $date = $request->query('date');
        $displayStatus = $request->query('display_status');

        $query = GuestBook::query()->with('company:id,name');

        if ($search !== '') {
            $query->where('guest_name', 'like', "%{$search}%");
        }

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        if ($displayStatus) {
            $query->where('display_status', $displayStatus);
        }

        $guestBooks = $query->orderByDesc('created_at')->paginate(15)->appends($request->query());
        $companies = Company::orderBy('name')->pluck('name', 'id');

        return view('admin.guest-books.index', compact('guestBooks', 'companies', 'search', 'companyId', 'date', 'displayStatus'));
    }

    public function updateStatus(UpdateGuestBookStatusRequest $request, GuestBook $guestBook): RedirectResponse
    {
        $guestBook->update([
            'display_status' => $request->validated('display_status'),
        ]);

        return redirect()->route('admin.guest-books.index')->with('success', 'Guest message display status updated.');
    }

    public function destroy(GuestBook $guestBook): RedirectResponse
    {
        $guestBook->delete();

        return redirect()->route('admin.guest-books.index')->with('success', 'Guest record deleted successfully.');
    }

    public function export(GuestBookIndexRequest $request)
    {
        $search = trim((string) $request->query('search', ''));
        $companyId = $request->query('company_id');
        $date = $request->query('date');
        $displayStatus = $request->query('display_status');

        $query = GuestBook::query()->with('company:id,name');

        if ($search !== '') {
            $query->where('guest_name', 'like', "%{$search}%");
        }

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        if ($displayStatus) {
            $query->where('display_status', $displayStatus);
        }

        $filename = 'guest-book-export-' . now()->format('YmdHis') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Guest Name', 'Company', 'Status', 'Message', 'Created At']);

            $query->orderByDesc('created_at')->chunkById(100, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id,
                        $row->guest_name,
                        $row->company?->name ?? 'Unknown',
                        $row->display_status,
                        strip_tags($row->message),
                        $row->created_at->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
