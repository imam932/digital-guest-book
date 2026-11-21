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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('guest_name', 'like', "%{$search}%")
                    ->orWhere('other_company', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($companyQuery) use ($search) {
                        $companyQuery->where('name', 'like', "%{$search}%");
                    });
            });
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
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('guest_name', 'like', "%{$search}%")
                    ->orWhere('other_company', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($companyQuery) use ($search) {
                        $companyQuery->where('name', 'like', "%{$search}%");
                    });
            });
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Guest Books');

        $headers = ['ID', 'Guest Name', 'Company', 'Status', 'Message', 'Created At'];
        $sheet->fromArray([$headers], null, 'A1');

        $rowNumber = 2;
        $query->orderByDesc('created_at')->chunkById(100, function ($rows) use ($sheet, &$rowNumber) {
            foreach ($rows as $row) {
                $sheet->setCellValue("A{$rowNumber}", $row->id);
                $sheet->setCellValue("B{$rowNumber}", $row->guest_name);
                $sheet->setCellValue("C{$rowNumber}", $row->resolvedCompanyName());
                $sheet->setCellValue("D{$rowNumber}", $row->display_status);
                $sheet->setCellValue("E{$rowNumber}", strip_tags($row->message));
                $sheet->setCellValue("F{$rowNumber}", $row->created_at->toDateTimeString());
                $rowNumber++;
            }
        });

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:F" . max(1, $rowNumber - 1));

        foreach (['A' => 10, 'B' => 24, 'C' => 24, 'D' => 14, 'E' => 50, 'F' => 20] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        $filename = 'guest-book-export-' . now()->format('YmdHis') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
