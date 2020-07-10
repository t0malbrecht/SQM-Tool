@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-9 pt-5">
                    <div class="d-flex">
                        <h1 class="pb-2">Mehrmalszahlung</h1>
                        <ongoingpayment-edit-button v-bind:ongoingpayment={{$ongoingPayment->id}}></ongoingpayment-edit-button>
                    </div>
                <div>
                    <div class="pr-5">
                        Beschreibung <strong>{{$ongoingPayment->description}}</strong>
                    </div>
                    <div class="pr-5">
                        Betrag: <strong>{{$ongoingPayment->grantedFunds}}</strong>
                    </div>
                    <div class="pr-5">
                        Fälligkeit: <strong>{{$ongoingPayment->due}}</strong>
                    </div>
                    <div class="pr-5">
                        Geplantes Startdatum: <strong>{{$ongoingPayment->plannedStartDate}}</strong>
                    </div>
                    <div class="pr-5">
                        Geplantes Enddatum: <strong>{{$ongoingPayment->plannedEndDate}}</strong>
                    </div>

                </div>
                <div class="pt-3">
                    <ongoingpayment-forumlar-button v-bind:id={{$ongoingPayment->id}}></ongoingpayment-forumlar-button>
                </div>
            </div>
        </div>
        <ongoingpaymenthistories-index-table class="pt-3" v-bind:id={{$ongoingPayment->id}}></ongoingpaymenthistories-index-table>
    </div>
@endsection
