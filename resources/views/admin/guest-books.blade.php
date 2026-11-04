@extends('layouts.admin')

@section('title', 'Guest Books')

@section('page-content')
    <div class="container-fluid py-4 px-4 px-xl-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="eyebrow text-primary mb-2">Management</p>
                <h1 class="h2 fw-bold mb-0">Guest Books</h1>
            </div>
            <button type="button" class="btn btn-primary">New guest book</button>
        </div>

        <div class="panel-card">
            <p class="text-muted mb-0">Guest book content and moderation tools will be added here.</p>
        </div>
    </div>
@endsection
