<?php

namespace App\Http\Controllers;

use App\OngoingPaymentHistory;
use Illuminate\Http\Request;

class OngoingPaymentHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function serveOngoingPaymentHistories(Request $request){
        $planning = htmlspecialchars($request->input('planning'));
        $sortBy = htmlspecialchars($request->input('sortBy'));
        $perPage = htmlspecialchars($request->input('perPage'));
        $startDate = htmlspecialchars($request->input('startDate'));
        $endDate = htmlspecialchars($request->input('endDate'));
        $ongoingPayment = htmlspecialchars($request->input('ongoingPayment'));
        $chargedFundsCenter = htmlspecialchars($request->input('chargedFundsCenter'));

        $sortWay = 'asc';
        if($request->input('sortDesc') == 'true')
            $sortWay = 'desc';
        if($sortBy == null or $sortBy == 'null'){
            $sortBy = 'plannedPaymentDate';
        }

        if($chargedFundsCenter != 'null' && $chargedFundsCenter != null){
            $query = OngoingPaymentHistory::with(['ongoingPayment.claim'])->whereHas('ongoingPayment', function($query) use ($chargedFundsCenter) {
                $query->where('chargedFundsCenter_id', '=', $chargedFundsCenter);});
        }else if($ongoingPayment != 'null' && $ongoingPayment != null){
            $query = OngoingPaymentHistory::with(['ongoingPayment'])->where
            ('ongoing_payment_id', '=', $ongoingPayment);
        }else{
            $query = OngoingPaymentHistory::with(['ongoingPayment.claim']);
        }

        if($startDate != 'null' && $startDate != null){
            if($endDate != 'null' && $endDate != null){
                $query->where('plannedPaymentDate', '>=', $startDate);
                $query->where('plannedPaymentDate', '<=', $endDate);
                $query->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('realPaymentDate', '>=', $startDate)
                        ->where('realPaymentDate', '<=', $endDate);
                });
            }else{
                $query->where('plannedPaymentDate', '>=', $startDate)
                    ->orWhere('realPaymentDate', '>=', $startDate);
            }
        }else if($endDate != 'null' && $endDate != null) {
            $query->where('plannedPaymentDate', '<=', $endDate)
                ->orWhere('realPaymentDate', '<=', $endDate);
        }

        $query->orderBy($sortBy, $sortWay);
        if($perPage != 'null' and $perPage != null){
            $query->paginate($perPage);
            $lastPage = $query->paginate($perPage)->lastPage();
        }
        $result = $query->get();
        if($planning != 'null' && $planning != null){
            $result = $result->groupBy('ongoing_payment_id');
            $result->toArray();
            $amountForEachOngoingPayment = array();
            foreach ($result as $value){
                $grantedFunds = 0;
                $spentFunds = 0;
                foreach($value as $item){
                    $grantedFunds = $grantedFunds + $item->grantedFunds;
                    $spentFunds = $spentFunds + $item->spentFunds;
                }
                $amountForEachOngoingPayment[] = ['title' => $value[0]->ongoingPayment->claim->title ,'notes' => $value[0]->ongoingPayment->notes, 'ongoing_payment_id' => $value[0]->ongoing_payment_id, 'description' =>  $value[0]->ongoingPayment->claim->title, 'grantedFunds' => $grantedFunds,  'spentFunds' => $spentFunds, 'favoredFundsCenter' => $value[0]->ongoingPayment->favoredFundsCenter->description];
            }
            $result = $amountForEachOngoingPayment;
        }

        return [$result, $lastPage ?? null];
    }

    public function update(OngoingPaymentHistory $ongoingPaymentHistory){
        $data = request()->validate([
            'grantedFunds' => 'required|numeric',
            'spentFunds' => '',
            'plannedPaymentDate' => 'required|date',
            'realPaymentDate' => '',
        ]);
        try {
            $ongoingPaymentHistory->update([
                'grantedFunds' => $data['grantedFunds'],
                'spentFunds' => $data['spentFunds'],
                'plannedPaymentDate' => $data['plannedPaymentDate'],
                'realPaymentDate' => $data['realPaymentDate'],
            ]);

        }catch (QueryException $ex){
            return response()->json($ex->getMessage(), 200);
        }

        return response()->json(null, 200);
    }

    public function delete(OngoingPaymentHistory $ongoingPaymentHistory){
        try {
            $ongoingPaymentHistory->delete();
        } catch (\Exception $e) {
            return response()->json(null, 423);
        }
        return response()->json(null, 200);
    }
}
