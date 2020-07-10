@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-9 pt-5">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h1>Sitzung</h1>
                </div>
                <div>
                    <div class="pr-5">
                        Datum: <strong>{{$meeting->date}}</strong>
                    </div>
                    <div class="pr-5">
                        Nummer des Meetings im Jahr: <strong>{{$meeting->number}}</strong>
                    </div>
                </div>
            </div>
        </div>
        <claim-index-table class="pt-3" v-bind:meeting={{$meeting}}></claim-index-table>
    </div>
@endsection
