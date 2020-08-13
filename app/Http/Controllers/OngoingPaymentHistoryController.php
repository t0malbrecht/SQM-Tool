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
        $id = htmlspecialchars($request->input('id'));
        $filter = htmlspecialchars($request->input('filter'));
        $sortBy = htmlspecialchars($request->input('sortBy'));
        $perPage = htmlspecialchars($request->input('perPage'));
        $startDate = htmlspecialchars($request->input('startDate'));
        $endDate = htmlspecialchars($request->input('endDate'));

        $sortWay = 'asc';
        if($request->input('sortDesc') == 'true')
            $sortWay = 'desc';
        if($sortBy == null or $sortBy == 'null'){
            $sortBy = 'plannedPaymentDate';
        }

        $query = OngoingPaymentHistory::with(['ongoingPayment']);
        if($id != null && $filter != null){
            $query->where('ongoing_payment_id', '=', $id);
        }
        if($filter != 'null' && $filter != null) {
            $query->where('grantedFunds', 'like', '%' . $filter . '%')
                ->orWhere('spentFunds', 'like', '%' . $filter . '%');
        }
        if($startDate != 'null' && $startDate != null)
            $query->where('plannedPaymentDate', '>=', $startDate)
                ->orWhere('realPaymentDate', '>=', $startDate);

        if($endDate != 'null' && $endDate != null)
            $query->where('plannedPaymentDate', '<=', $endDate)
                ->orWhere('realPaymentDate', '<=', $endDate);
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
                $amountForEachOngoingPayment[] = ['ongoing_payment_id' => $value[0]->ongoing_payment_id, 'description' =>  $value[0]->ongoingPayment->claim->title, 'grantedFunds' => $grantedFunds,  'spentFunds' => $spentFunds, 'favoredFundsCenter' => $value[0]->ongoingPayment->favoredFundsCenter->description];
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
}
