@extends('layouts.public')

@section('title', 'Message Wall')
@section('meta_description', 'Live guest book message wall for the current event.')

@section('page-content')
    <div class="message-wall-shell">
        <div id="messageWall" class="message-wall" aria-live="polite" aria-atomic="false"></div>
        <div id="messagePopup" class="message-popup d-none" role="status" aria-live="polite"></div>
    </div>
@endsection
