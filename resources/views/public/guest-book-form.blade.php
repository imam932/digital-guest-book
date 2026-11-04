@extends('layouts.public')

@section('title', 'Guest Book Registration')
@section('meta_description', 'Submit a guest message for the current event.')

@section('page-content')
    <section class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="guest-form-card shadow-sm">
                    <div class="mb-4 text-center">
                        <p class="eyebrow mb-2">Guest registration</p>
                        <h1 class="h2 fw-bold mb-1">Sign the guest book</h1>
                        <p class="text-secondary mb-0">Share your message for the event and select your company.</p>
                    </div>

                    <div id="guestBookAlert" class="alert d-none" role="alert"></div>

                    <form id="guestBookForm" method="POST" action="{{ route('guest-book.store') }}" novalidate>
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="guest_name" class="form-label">Guest name</label>
                                <input id="guest_name" name="guest_name" type="text" class="form-control form-control-lg" maxlength="255" required>
                                <div class="invalid-feedback d-block" data-error-for="guest_name"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="company_id" class="form-label">Company</label>
                                <select id="company_id" name="company_id" class="form-select form-select-lg" required>
                                    <option value="">Select an active company</option>
                                </select>
                                <div class="invalid-feedback d-block" data-error-for="company_id"></div>
                            </div>

                            <div class="col-12">
                                <label for="message" class="form-label">Message</label>
                                <textarea id="message" name="message" class="form-control" rows="6" maxlength="2000" placeholder="Write a warm message for the event..." required></textarea>
                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                    <span>Minimum 10 characters</span>
                                    <span id="messageCounter">0 / 2000</span>
                                </div>
                                <div class="invalid-feedback d-block" data-error-for="message"></div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" id="submitGuestBook" class="btn btn-primary btn-lg">
                                <span class="submit-text">Submit message</span>
                                <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
