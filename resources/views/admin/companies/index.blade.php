@extends('layouts.admin')

@section('title', 'Manage Companies')

@section('page-content')
    <div class="container-fluid py-4 px-4 px-xl-5">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <p class="eyebrow text-primary mb-2">Directory</p>
                <h1 class="h2 fw-bold mb-0">Companies</h1>
            </div>
            <button type="button" class="btn btn-primary mt-3 mt-md-0" data-bs-toggle="collapse" data-bs-target="#companyFormPanel">Create company</button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="collapse {{ $errors->any() || request()->routeIs('admin.companies.create') ? 'show' : '' }} mb-4" id="companyFormPanel">
            <div class="panel-card">
                <h2 class="h5 fw-semibold mb-3">{{ isset($company) ? 'Edit company' : 'Add company' }}</h2>
                <form method="POST" action="{{ isset($company) ? route('admin.companies.update', $company) : route('admin.companies.store') }}">
                    @csrf
                    @if (isset($company))
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Company name</label>
                            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $company->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status', $company->status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $company->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">{{ isset($company) ? 'Update' : 'Save' }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel-card">
            <form method="GET" action="{{ route('admin.companies.index') }}" class="row g-3 mb-3 align-items-end">
                <div class="col-md-10">
                    <label for="company_search" class="form-label">Search company</label>
                    <input id="company_search" name="search" type="text" class="form-control" value="{{ $search }}" placeholder="Search company name...">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($companies as $company)
                            <tr>
                                <td>{{ $company->name }}</td>
                                <td>
                                    <span class="badge {{ $company->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ ucfirst($company->status) }}
                                    </span>
                                </td>
                                <td>{{ $company->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('admin.companies.toggle-status', $company) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $company->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                                {{ $company->status === 'active' ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.companies.destroy', $company) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this company? This action is blocked if guest records reference it.')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No companies found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($companies->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $companies->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
