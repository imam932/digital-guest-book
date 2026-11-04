@extends('layouts.admin')

@section('title', 'Admin Dashboard | Digital Guest Book')
@section('meta_description', 'Overview of event guest activity and company records for the admin dashboard.')

@section('page-content')
    <div class="container-fluid py-4 px-4 px-xl-5">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <p class="eyebrow text-primary mb-2">Overview</p>
                <h1 class="h2 fw-bold mb-0">Dashboard</h1>
            </div>
            <a href="{{ route('admin.guest-books.export') }}" class="btn btn-primary mt-3 mt-md-0">Export insights</a>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-header">
                        <span>Total entries</span>
                        <span class="badge bg-primary-subtle text-primary">Live</span>
                    </div>
                    <div class="metric-value">{{ $stats['total_entries'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-header">
                        <span>Companies</span>
                        <span class="badge bg-success-subtle text-success">Active</span>
                    </div>
                    <div class="metric-value">{{ $stats['total_companies'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-header">
                        <span>Pending</span>
                        <span class="badge bg-warning-subtle text-warning">Review</span>
                    </div>
                    <div class="metric-value">{{ $stats['pending_messages'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-header">
                        <span>Displayed</span>
                        <span class="badge bg-info-subtle text-info">Live</span>
                    </div>
                    <div class="metric-value">{{ $stats['displayed_messages'] }}</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="panel-card h-100">
                    <div class="panel-head">
                        <h2 class="h5 fw-semibold mb-0">Latest guest messages</h2>
                        <a href="{{ route('admin.guest-books.index') }}" class="small text-primary text-decoration-none">View all</a>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestMessages as $message)
                                    <tr>
                                        <td>{{ $message->guest_name }}</td>
                                        <td>{{ $message->company?->name ?? 'Unknown' }}</td>
                                        <td>
                                            <span class="badge {{ $message->display_status === 'displayed' ? 'bg-success-subtle text-success' : ($message->display_status === 'pending' ? 'bg-warning-subtle text-warning' : 'bg-secondary-subtle text-secondary') }}">
                                                {{ ucfirst($message->display_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $message->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No guest messages yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="panel-card h-100">
                    <div class="panel-head">
                        <h2 class="h5 fw-semibold mb-0">Quick actions</h2>
                    </div>
                    <div class="d-grid gap-3 mt-3">
                        <a href="{{ route('admin.guest-books.index') }}" class="btn btn-primary btn-lg">Moderate guest entries</a>
                        <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-primary btn-lg">Manage companies</a>
                        <a href="{{ route('message-wall') }}" target="_blank" class="btn btn-outline-secondary btn-lg">Open display wall</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
