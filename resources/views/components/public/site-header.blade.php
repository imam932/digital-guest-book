@php($eventSettings = \App\Models\EventSetting::current())

<header class="public-header">
    <a class="public-header__brand" href="{{ url('/') }}" aria-label="{{ $eventSettings?->event_name ?? 'Digital Guest Book' }}">
        <!-- <span class="brand-mark">DG</span> -->
         <img src="{{ asset('img/LOGO-SIER-52.png') }}" alt="{{ $eventSettings?->event_name ?? 'Digital Guest Book' }} Logo" class="brand-logo">
         <img src="{{ asset('img/Logo-sier-white.png') }}" alt="{{ $eventSettings?->event_name ?? 'Digital Guest Book' }} Logo" class="brand-logo--small">
    </a>
</header>
