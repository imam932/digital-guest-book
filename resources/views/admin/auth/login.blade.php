@extends('layouts.base')

@section('title', 'Admin Login')
@section('body_class', 'bg-light')

@section('content')
    <div class="min-vh-100 d-flex align-items-center justify-content-center px-3 py-5">
        <div class="card shadow-sm border-0" style="width: min(100%, 420px);">
            <div class="card-body p-4 p-lg-5">
                <div class="text-center mb-4">
                    <div class="brand-mark mx-auto mb-3">DG</div>
                    <h1 class="h3 fw-bold mb-1">Admin Sign In</h1>
                    <p class="text-muted mb-0">Access the digital guest book dashboard</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" required autofocus>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">Sign In</button>
                </form>
            </div>
        </div>
    </div>
@endsection
