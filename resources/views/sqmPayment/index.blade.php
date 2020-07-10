@extends('layouts.app')

@section('content')
    <head><meta name="csrf-token" content="{{ csrf_token() }}"></head>
    <div class="container">
        <sqmpayment-index-table></sqmpayment-index-table>
    </div>
@endsection
