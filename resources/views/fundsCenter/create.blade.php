@extends('layouts.app')

@section('content')
    <head><meta name="csrf-token" content="{{ csrf_token() }}"></head>
    <div class="container">
        <div class="text-center pb-3">
            <h2 class="mx-auto">Finanzstelle erstellen</h2>
        </div>
        <fundscenter-create-formular></fundscenter-create-formular>
    </div>
@endsection
