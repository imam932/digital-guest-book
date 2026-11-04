@extends('layouts.public')

@section('title', 'Digital Guest Book | Corporate Events')
@section('meta_description', 'Interactive guest book experience for corporate events and executive gatherings.')

@section('page-content')
    <section class="public-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <p class="eyebrow mb-3">Corporate event experience</p>
                    <h1 class="display-4 fw-bold mb-3">Create a lasting first impression.</h1>
                    <p class="lead text-secondary mb-4">
                        Capture attendee stories, celebrate milestones, and collect memorable messages in one interactive guest book.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a href="#guest-book" class="btn btn-primary btn-lg px-4">Sign the guest book</a>
                        <a href="/admin" class="btn btn-outline-primary btn-lg px-4">Open admin dashboard</a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="hero-card shadow-lg">
                        <div class="card-body p-4 p-lg-5">
                            <span class="badge bg-primary-subtle text-primary mb-3">Event spotlight</span>
                            <h2 class="h4 fw-semibold mb-3">Annual Leadership Summit</h2>
                            <p class="text-secondary mb-4">A celebration of innovation, team culture, and shared achievements across the company.</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0">4,200 guests</li>
                                <li class="list-group-item px-0">230 messages today</li>
                                <li class="list-group-item px-0">19 photo submissions</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5" id="guest-book">
        <div class="row g-4 mb-5">
            <div class="col-md-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="stat-value">4.2k</div>
                    <div class="stat-label">Guest entries</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="stat-value">96%</div>
                    <div class="stat-label">Completion rate</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="stat-value">340</div>
                    <div class="stat-label">Photos shared</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="stat-value">12</div>
                    <div class="stat-label">Live updates</div>
                </div>
            </div>
        </div>

        <div class="row align-items-start g-4">
            <div class="col-lg-7">
                <div class="section-heading mb-4">
                    <p class="eyebrow">Guest book</p>
                    <h2 class="h3 fw-bold mb-0">Recent messages</h2>
                </div>

                <div class="guest-entry-card mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>Amelia Brooks</strong>
                            <div class="text-muted small">Marketing Director</div>
                        </div>
                        <span class="badge bg-light text-dark">Today</span>
                    </div>
                    <p class="mb-0 text-secondary">Thank you for creating such a meaningful event experience. The attention to detail made every guest feel genuinely welcome.</p>
                </div>

                <div class="guest-entry-card mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>Daniel Nguyen</strong>
                            <div class="text-muted small">Operations Lead</div>
                        </div>
                        <span class="badge bg-light text-dark">Today</span>
                    </div>
                    <p class="mb-0 text-secondary">It was a pleasure connecting with the team in such a warm and energizing setting. Looking forward to the next edition.</p>
                </div>

                <div class="guest-entry-card mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>Nadia Ali</strong>
                            <div class="text-muted small">Client Success Manager</div>
                        </div>
                        <span class="badge bg-light text-dark">Yesterday</span>
                    </div>
                    <p class="mb-0 text-secondary">The hospitality, flow of the program, and thoughtful guest experience all stood out. Truly memorable.</p>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="feature-card h-100">
                    <p class="eyebrow">Event highlights</p>
                    <h3 class="h4 fw-bold">Built for professional environments.</h3>
                    <ul class="feature-list mt-4">
                        <li>Responsive guest sign-in experience</li>
                        <li>Real-time moderation for admin users</li>
                        <li>Touch-friendly mobile and desktop layouts</li>
                        <li>Clean public and private experience separation</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
