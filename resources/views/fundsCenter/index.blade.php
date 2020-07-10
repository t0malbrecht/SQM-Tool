@extends('layouts.app')

@section('content')
    <head><meta name="csrf-token" content="{{ csrf_token() }}"></head>
    <div class="container">
        <fundscenter-index-table></fundscenter-index-table>
    </div>
@endsection
