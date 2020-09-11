@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-9 pt-5">
                <div class="d-flex">
                    <div class="d-flex">
                        <h1 class="pb-2">Einmalzahlung</h1>
                        <onetimepayment-edit-button v-bind:onetimepayment={{$oneTimePayment->id}}></onetimepayment-edit-button>
                        <proofofuse-create-button v-bind:payment1="{{$oneTimePayment}}"></proofofuse-create-button>
                    </div>
                </div>
                <div>
                    <div class="pr-5">
                        Titel <strong>{{$oneTimePayment->claim->title}}</strong>
                    </div>
                    <div class="pr-5">
                        gewährter Betrag: <strong>{{$oneTimePayment->grantedFunds}}</strong>
                    </div>
                    <div class="pr-5">
                        ausgegebener Betrag: <strong>{{$oneTimePayment->spentsFunds ?? 0}}</strong>
                    </div>
                    <div class="pr-5">
                        Auszugeben bis: <strong>{{$oneTimePayment->dueDate}}</strong>
                    </div>
                    <div class="pr-5">
                        Ausgabetermin: <strong>{{$oneTimePayment->spentDate ?? 'Noch nicht ausgegeben'}}</strong>
                    </div>
                    <div class="pr-5">
                        Notizen: <strong>{{$oneTimePayment->requirements}}</strong>
                    </div>
                    <div class="pr-5">
                        Anforderungen: <strong>{{$oneTimePayment->requirements}}</strong>
                    </div>
                </div>
                <div class="pt-3">
                    <onetimepayment-forumlar-button v-bind:id={{$oneTimePayment->id}}></onetimepayment-forumlar-button>
                </div>
            </div>
        </div>
    </div>
@endsection
