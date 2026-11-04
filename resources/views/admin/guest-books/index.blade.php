@extends('layouts.admin')

@section('title', 'Guest Book Records')

@section('page-content')
    <div class="container-fluid py-4 px-4 px-xl-5">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <p class="eyebrow text-primary mb-2">Records</p>
                <h1 class="h2 fw-bold mb-0">Guest Book Records</h1>
            </div>
            <div class="d-flex gap-2 mt-3 mt-md-0">
                <a href="{{ route('admin.guest-books.export', request()->query()) }}" class="btn btn-outline-primary">Export CSV</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="panel-card mb-4">
            <form method="GET" action="{{ route('admin.guest-books.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search guest name</label>
                    <input id="search" name="search" type="text" class="form-control" value="{{ request('search') }}" placeholder="Search guest name...">
                </div>

                <div class="col-md-3">
                    <label for="company_id" class="form-label">Company</label>
                    <select id="company_id" name="company_id" class="form-select">
                        <option value="">All companies</option>
                        @foreach ($companies as $id => $name)
                            <option value="{{ $id }}" {{ request('company_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="date" class="form-label">Date</label>
                    <input id="date" name="date" type="date" class="form-control" value="{{ request('date') }}">
                </div>

                <div class="col-md-2">
                    <label for="display_status" class="form-label">Status</label>
                    <select id="display_status" name="display_status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" {{ request('display_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="displayed" {{ request('display_status') === 'displayed' ? 'selected' : '' }}>Displayed</option>
                        <option value="hidden" {{ request('display_status') === 'hidden' ? 'selected' : '' }}>Hidden</option>
                    </select>
                </div>

                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>

        <div class="panel-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Message</th>
                            <th>Created</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($guestBooks as $guestBook)
                            <tr>
                                <td>{{ $guestBook->guest_name }}</td>
                                <td>{{ $guestBook->company?->name ?? 'Unknown' }}</td>
                                <td>
                                    <div class="message-preview" data-bs-toggle="modal" data-bs-target="#messageModal{{ $guestBook->id }}" style="cursor:pointer; max-width: 320px;">
                                        {{ Str::limit(strip_tags($guestBook->message), 80) }}
                                    </div>
                                </td>
                                <td>{{ $guestBook->created_at->format('M d, Y') }}</td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-warning-subtle text-warning',
                                            'displayed' => 'bg-success-subtle text-success',
                                            'hidden' => 'bg-secondary-subtle text-secondary',
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$guestBook->display_status] ?? 'bg-light text-dark' }}">
                                        {{ ucfirst($guestBook->display_status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#messageModal{{ $guestBook->id }}">View</button>
                                        <form method="POST" action="{{ route('admin.guest-books.status', $guestBook) }}">
                                            @csrf
                                            <input type="hidden" name="display_status" value="{{ $guestBook->display_status === 'displayed' ? 'hidden' : 'displayed' }}">
                                            <button type="submit" class="btn btn-sm {{ $guestBook->display_status === 'displayed' ? 'btn-outline-secondary' : 'btn-outline-success' }}" onclick="return confirm('Update display status for this record?')">
                                                {{ $guestBook->display_status === 'displayed' ? 'Hide' : 'Show' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.guest-books.destroy', $guestBook) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this guest record permanently?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="messageModal{{ $guestBook->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Guest Message</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Name:</strong> {{ $guestBook->guest_name }}</p>
                                            <p><strong>Company:</strong> {{ $guestBook->company?->name ?? 'Unknown' }}</p>
                                            <p><strong>Status:</strong> {{ ucfirst($guestBook->display_status) }}</p>
                                            <p class="mb-0"><strong>Message:</strong></p>
                                            <div class="mt-2 text-body-secondary">{{ $guestBook->message }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($guestBooks->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $guestBooks->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
