@extends('layouts.app')

@section('content')
    <div class="container">
        <proofofuse-show v-bind:proofofuse="{{$proofOfUse}}"></proofofuse-show>
    </div>
@endsection
