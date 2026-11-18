@php($eventSettings = \App\Models\EventSetting::current())

<header class="public-header">
    <a class="public-header__brand" href="{{ url('/') }}" aria-label="{{ $eventSettings?->event_name ?? 'Digital Guest Book' }}">
        <span class="brand-mark">DG</span>
    </a>
</header>
