@php($eventSettings = \App\Models\EventSetting::current())

@extends('layouts.public')

@section('title', 'Message Wall')
@section('meta_description', 'Live guest book message wall for the current event.')

@section('page-content')
    @php($mainBackground = $eventSettings?->mainBackgroundUrl())

    <div class="message-wall-shell" @if($mainBackground) style="background-image: linear-gradient(135deg, rgba(11, 18, 32, 0.9), rgba(37, 99, 235, 0.72)), url('{{ $mainBackground }}');" @endif>
        <div id="messageWall" class="message-wall" aria-live="polite" aria-atomic="false"></div>
        <div id="messagePopup" class="message-popup d-none" role="status" aria-live="polite"></div>
    </div>
@endsection
