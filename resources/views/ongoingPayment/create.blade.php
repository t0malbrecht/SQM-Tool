@extends('layouts.app')

@section('content')
    <head><meta name="csrf-token" content="{{ csrf_token() }}"></head>
    <div class="container">
        <div class="text-center pb-3">
            <h2 class="mx-auto">Mehrmalszahlung erstellen</h2>
        </div>
        <ongoingpayment-create-formular></ongoingpayment-create-formular>
    </div>
@endsection
