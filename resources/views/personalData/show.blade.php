@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-9 pt-5">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h1 class="h4">Persönliche Daten</h1>
                        <a href="/personalData/{{$personalData->id}}/edit">Persönliche Daten bearbeiten</a>
                </div>
                <div>
                    <div class="pr-5">
                        Vorname: <strong>{{$personalData->firstname}}</strong>
                    </div>
                    <div class="pr-5">
                        Nachname: <strong>{{$personalData->lastname}}</strong>
                    </div>
                    <div class="pr-5">
                        Telefonnummer: <strong>{{$personalData->number}}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
