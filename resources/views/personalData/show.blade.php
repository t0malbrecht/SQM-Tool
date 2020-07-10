@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-9 pt-5">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h1 class="h4">Username: {{$user->username}}</h1>
                    @can('update', $user->personalData, PersonalData::class)
                        <a href="/personalData/{{$user->id}}/edit">Persönliche Daten bearbeiten</a>
                    @endcan
                </div>
                <div>
                    <div class="pr-5">
                        Vorname: <strong>{{$user->personalData->firstname}}</strong>
                    </div>
                    <div class="pr-5">
                        Nachname: <strong>{{$user->personalData->lastname}}</strong>
                    </div>
                    <div class="pr-5">
                        Telefonnummer: <strong>{{$user->personalData->number}}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
