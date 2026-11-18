<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompanyRequest;
use App\Http\Requests\Admin\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $companies = Company::query()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.companies.index', compact('companies', 'search'));
    }

    public function create(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $companies = Company::query()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.companies.index', compact('companies', 'search'));
    }

    public function edit(Company $company): View
    {
        $companies = Company::orderBy('name')->paginate(10);

        return view('admin.companies.index', compact('company', 'companies'));
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $rows = Excel::toArray([], $request->file('file'));
        $sheetRows = $rows[0] ?? [];

        if (empty($sheetRows)) {
            return redirect()->route('admin.companies.index')->with('error', 'The uploaded file does not contain any company data.');
        }

        $created = 0;
        $skipped = 0;

        foreach ($sheetRows as $row) {
            $name = $this->extractCompanyName($row);

            if ($name === null || $name === '') {
                continue;
            }

            $company = Company::firstOrNew(['name' => $name]);

            if ($company->exists) {
                $skipped++;
                continue;
            }

            $company->status = 'active';
            $company->save();
            $created++;
        }

        $message = $created > 0
            ? "Imported {$created} company name(s)."
            : 'No new company name(s) were imported from the file.';

        if ($skipped > 0) {
            $message .= " Skipped {$skipped} duplicate or existing company name(s).";
        }

        return redirect()->route('admin.companies.index')->with('success', $message);
    }

    public function downloadTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'company_name');
        $sheet->setCellValue('A2', 'PT Contoh');
        $sheet->setCellValue('A3', 'CV Demo');
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getStyle('A1')->getFont()->setBold(true);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'company-import-template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        Company::create($request->validated());

        return redirect()->route('admin.companies.index')->with('success', 'Company created successfully.');
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $company->update($request->validated());

        return redirect()->route('admin.companies.index')->with('success', 'Company updated successfully.');
    }

    public function toggleStatus(Company $company): RedirectResponse
    {
        $company->update([
            'status' => $company->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('admin.companies.index')->with('success', 'Company status updated successfully.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        try {
            $company->delete();
        } catch (\RuntimeException $exception) {
            return redirect()->route('admin.companies.index')->with('error', 'This company cannot be deleted because it is still referenced by guest book records.');
        }

        return redirect()->route('admin.companies.index')->with('success', 'Company deleted successfully.');
    }

    private function extractCompanyName(mixed $row): ?string
    {
        if (! is_array($row)) {
            return null;
        }

        foreach (['company_name', 'name'] as $key) {
            if (array_key_exists($key, $row)) {
                $value = trim((string) $row[$key]);

                if ($value !== '' && strtolower(str_replace([' ', '_'], '', $value)) !== 'companyname') {
                    return $value;
                }
            }
        }

        foreach ($row as $value) {
            if (is_scalar($value)) {
                $candidate = trim((string) $value);
                $normalized = strtolower(str_replace([' ', '_'], '', $candidate));

                if ($candidate !== '' && $normalized !== 'companyname' && $normalized !== 'name') {
                    return $candidate;
                }
            }
        }

        return null;
    }
}
