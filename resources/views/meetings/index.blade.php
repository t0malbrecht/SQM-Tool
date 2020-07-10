@extends('layouts.app')

@section('content')
    <head><meta name="csrf-token" content="{{ csrf_token() }}"></head>
    <div class="container">
        <meeting-index-table></meeting-index-table>
    </div>
@endsection
