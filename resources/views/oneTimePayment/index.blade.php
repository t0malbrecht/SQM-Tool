@extends('layouts.app')

@section('content')
    <head><meta name="csrf-token" content="{{ csrf_token() }}"></head>
    <div class="container">
        <onetimepayment-index-table></onetimepayment-index-table>
    </div>
@endsection
