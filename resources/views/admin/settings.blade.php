@extends('layouts.admin')

@section('title', 'Event Settings')

@section('page-content')
    <div class="container-fluid py-4 px-4 px-xl-5">
        <div class="mb-4">
            <p class="eyebrow text-primary mb-2">Configuration</p>
            <h1 class="h2 fw-bold mb-0">Event Settings</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="panel-card">
            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">
                    <div class="col-lg-6">
                        <label for="event_name" class="form-label">Event name</label>
                        <input id="event_name" name="event_name" type="text" class="form-control @error('event_name') is-invalid @enderror" value="{{ old('event_name', $settings->event_name ?? '') }}" maxlength="255" required>
                        @error('event_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="event_description" class="form-label">Event description</label>
                        <textarea id="event_description" name="event_description" rows="4" class="form-control @error('event_description') is-invalid @enderror" maxlength="2000">{{ old('event_description', $settings->event_description ?? '') }}</textarea>
                        @error('event_description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-6">
                        <label for="main_background" class="form-label">Main display background</label>
                        <input id="main_background" name="main_background" type="file" accept="image/jpeg,image/png,image/webp" class="form-control @error('main_background') is-invalid @enderror">
                        @error('main_background')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @if ($settings?->mainBackgroundUrl())
                            <div class="mt-3">
                                <div class="small text-muted mb-2">Current image</div>
                                <img id="mainBackgroundPreview" src="{{ $settings->mainBackgroundUrl() }}" alt="Main display background preview" class="img-fluid rounded border" style="max-height: 200px; object-fit: cover; width: 100%;">
                            </div>
                        @else
                            <div class="mt-3">
                                <div class="small text-muted mb-2">Current image</div>
                                <div id="mainBackgroundPreview" class="rounded border border-dashed text-muted d-flex align-items-center justify-content-center" style="height: 180px; background: linear-gradient(135deg, rgba(15,23,42,0.08), rgba(96,165,250,0.18));">No image uploaded</div>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-6">
                        <label for="form_background" class="form-label">Guest form background</label>
                        <input id="form_background" name="form_background" type="file" accept="image/jpeg,image/png,image/webp" class="form-control @error('form_background') is-invalid @enderror">
                        @error('form_background')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @if ($settings?->formBackgroundUrl())
                            <div class="mt-3">
                                <div class="small text-muted mb-2">Current image</div>
                                <img id="formBackgroundPreview" src="{{ $settings->formBackgroundUrl() }}" alt="Guest form background preview" class="img-fluid rounded border" style="max-height: 200px; object-fit: cover; width: 100%;">
                            </div>
                        @else
                            <div class="mt-3">
                                <div class="small text-muted mb-2">Current image</div>
                                <div id="formBackgroundPreview" class="rounded border border-dashed text-muted d-flex align-items-center justify-content-center" style="height: 180px; background: linear-gradient(135deg, rgba(255,255,255,0.8), rgba(191,219,254,0.35));">No image uploaded</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Save settings</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bindPreview = (inputId, previewId) => {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);

                if (!input || !preview) {
                    return;
                }

                input.addEventListener('change', (event) => {
                    const [file] = event.target.files;
                    if (!file) {
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (loadEvent) => {
                        if (preview.tagName === 'IMG') {
                            preview.src = loadEvent.target.result;
                            return;
                        }

                        const image = document.createElement('img');
                        image.src = loadEvent.target.result;
                        image.alt = previewId;
                        image.className = 'img-fluid rounded border';
                        image.style.maxHeight = '200px';
                        image.style.objectFit = 'cover';
                        image.style.width = '100%';
                        preview.replaceWith(image);
                    };
                    reader.readAsDataURL(file);
                });
            };

            bindPreview('main_background', 'mainBackgroundPreview');
            bindPreview('form_background', 'formBackgroundPreview');
        });
    </script>
@endsection
