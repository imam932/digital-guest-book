@extends('layouts.admin')

@section('title', 'Admin Dashboard | Digital Guest Book')
@section('meta_description', 'Overview of guest activity and event engagement for the admin dashboard.')

@section('page-content')
    <div class="container-fluid py-4 px-4 px-xl-5">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <p class="eyebrow text-primary mb-2">Overview</p>
                <h1 class="h2 fw-bold mb-0">Dashboard</h1>
            </div>
            <button type="button" class="btn btn-primary mt-3 mt-md-0">Export insights</button>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-header">
                        <span>Total guests</span>
                        <span class="badge bg-primary-subtle text-primary">+14%</span>
                    </div>
                    <div class="metric-value">4,238</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-header">
                        <span>Messages</span>
                        <span class="badge bg-success-subtle text-success">+8%</span>
                    </div>
                    <div class="metric-value">1,489</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-header">
                        <span>Photos</span>
                        <span class="badge bg-warning-subtle text-warning">+12%</span>
                    </div>
                    <div class="metric-value">348</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-header">
                        <span>Live check-ins</span>
                        <span class="badge bg-info-subtle text-info">+5%</span>
                    </div>
                    <div class="metric-value">639</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="panel-card h-100">
                    <div class="panel-head">
                        <h2 class="h5 fw-semibold mb-0">Recent guest activity</h2>
                        <span class="text-muted small">Updated 5 minutes ago</span>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Amelia Brooks</td>
                                    <td>Northstar Labs</td>
                                    <td><span class="badge bg-success-subtle text-success">Checked in</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                                </tr>
                                <tr>
                                    <td>Daniel Nguyen</td>
                                    <td>BluePeak Studio</td>
                                    <td><span class="badge bg-warning-subtle text-warning">Pending</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary">Review</button></td>
                                </tr>
                                <tr>
                                    <td>Nadia Ali</td>
                                    <td>SummitWorks</td>
                                    <td><span class="badge bg-success-subtle text-success">Checked in</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                                </tr>
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
                        <button class="btn btn-primary btn-lg">Approve new entries</button>
                        <button class="btn btn-outline-primary btn-lg">Moderate messages</button>
                        <button class="btn btn-outline-secondary btn-lg">Download reports</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
