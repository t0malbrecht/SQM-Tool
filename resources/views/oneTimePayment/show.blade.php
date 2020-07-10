@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-9 pt-5">
                <div class="d-flex">
                    <h1 class="pb-2">Einmalzahlung</h1>
                </div>
                <div>
                    <div class="pr-5">
                        Beschreibung <strong>{{$oneTimePayment->description}}</strong>
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
                        Bedingungen: <strong>{{$oneTimePayment->requirements}}</strong>
                    </div>
                </div>
                <div class="pt-3">
                    <onetimepayment-forumlar-button v-bind:id={{$oneTimePayment->id}}></onetimepayment-forumlar-button>
                </div>
            </div>
        </div>
    </div>
@endsection
