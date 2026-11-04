<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompanyRequest;
use App\Http\Requests\Admin\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
}
