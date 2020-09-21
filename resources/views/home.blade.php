@extends('layouts.app')

@section('content')
    <div class="row pb-3">
        <div class="col-12 text-center">
            <div class="h2">Willkommen, {{auth()->user()->personalData->firstname}}</div>
        </div>
    </div>
    <div class="row pt-3 text-center">
        <div class="col-2">
        </div>
        <clickable-blop icon="meetings" link="meetings" title="Sitzungen"></clickable-blop>
        <div class="col-1">
        </div>
        <clickable-blop icon="planning" link="planning" title="Planungsübersicht"></clickable-blop>
        <div class="col-1">
        </div>
        <clickable-blop icon="fundsCenter" link="fundsCenters" title="Finanzstellen"></clickable-blop>
        <div class="col-2">
        </div>
    </div>
    <div class="row pt-5 text-center">
        <div class="col-2">
        </div>
        <clickable-blop icon="sqmPayment" link="sqmPayments" title="SQM-Zahlungen"></clickable-blop>
        <div class="col-1">
        </div>
        @can('viewAny')
            <clickable-blop icon="users" link="users" title="Nutzer"></clickable-blop>
            <div class="col-1">
            </div>
        @endcan
        </div>
    </div>
@endsection
