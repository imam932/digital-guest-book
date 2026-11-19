@php($eventSettings = \App\Models\EventSetting::current())

<header class="public-header">
    <a class="public-header__brand" href="{{ url('/') }}" aria-label="{{ $eventSettings?->event_name ?? 'Digital Guest Book' }}">
        <img src="{{ asset('img/Logo-sier-white-n.png') }}" alt="{{ $eventSettings?->event_name ?? 'Digital Guest Book' }} Logo" class="brand-logo--small">
        <img src="{{ asset('img/LOGO-SIER-52-n.png') }}" alt="{{ $eventSettings?->event_name ?? 'Digital Guest Book' }} Logo" class="brand-logo">
    </a>
</header>
