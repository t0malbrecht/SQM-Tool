@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-9 pt-5">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h1 class="h4">Meeting bearbeiten</h1>
                </div>
                <div>
                    <div class="pr-5">
                        Datum: <strong>{{$meeting->date}}</strong>
                    </div>
                    <div class="pr-5">
                        Nummer des Meetings im Jahr: <strong>{{$meetig->number}}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
