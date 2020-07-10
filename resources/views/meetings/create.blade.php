@extends('layouts.app')

@section('content')
    <head><meta name="csrf-token" content="{{ csrf_token() }}"></head>
    <div class="container">
        <div class="text-center pb-3">
            <h2 class="mx-auto">Sitzung erstellen</h2>
        </div>
        <meeting-create-formular></meeting-create-formular>
    </div>
@endsection
