@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="/personalData/{{$user->id}}" enctype="multipart/form-data" method="post">
            @csrf
            @method('PATCH')

            <div class="row">
                <div class="col-8 offset-2">

                    <div class="row">
                        <h1>Persönliche Daten bearbeiten</h1>
                    </div>

                    <div class="form-group row">
                        <label for="title" class="col-md-4 col-form-label">Vorname</label>
                        <input
                            id="firstname"
                            type="text"
                            class="form-control @error('firstname') is-invalid @enderror"
                            name="firstname"
                            value="{{ old('firstname') ?? $user->personalData->firstname }}" autocomplete="firstname">

                        @error('firstname')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group row">
                        <label for="lastname" class="col-md-4 col-form-label">Nachname</label>
                        <input
                            id="lastname"
                            type="text"
                            class="form-control @error('lastname') is-invalid @enderror"
                            name="lastname"
                            value="{{ old('lastname') ?? $user->personalData->lastname }}" autocomplete="lastname">

                        @error('lastname')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group row">
                        <label for="number" class="col-md-4 col-form-label">Telefonnummer</label>
                        <input
                            id="number"
                            type="text"
                            class="form-control @error('number') is-invalid @enderror"
                            name="number"
                            value="{{ old('number') ?? $user->personalData->number}}" autocomplete="number">

                        @error('number')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="row pt-4">
                        <button class="btn btn-primary">Änderungen speichern</button>
                    </div>
                </div>
    </div>
@endsection
