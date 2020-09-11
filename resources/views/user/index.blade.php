@extends('layouts.app')

@section('content')
    <head><meta name="csrf-token" content="{{ csrf_token() }}"></head>
    <div class="container">
        <user-index-table></user-index-table>
    </div>
@endsection
