<?php

namespace App\Http\Controllers;

use App\SqmPayment;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SqmPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('sqmPayment.index');
    }

    public function create(){
        return view('sqmPayment.create');
    }

    public function store(){
        $data = request()->validate([
            'startDate' => 'required|date',
            'dueDate' => 'required|date',
            'amount' => 'required|numeric',
            'fundsCenter' => 'required|numeric',
        ]);

        try {
            $sqmPayment = SqmPayment::create([
                'fundsCenter_id' => $data['fundsCenter'],
                'startDate' => $data['startDate'],
                'dueDate' => $data['dueDate'],
                'amount' => $data['amount'],
            ]);
            $sqmPayment->save();
        }catch (QueryException $ex){
            return response()->json(null, 423);
        }

        return response()->json(null, 200);
    }

    public function serveSqmPayments(Request $request){
        $sum = htmlspecialchars($request->input('sum'));
        $filter = htmlspecialchars($request->input('filter'));
        $sortBy = htmlspecialchars($request->input('sortBy'));
        $perPage = htmlspecialchars($request->input('perPage'));
        $chargedFundsCenter = htmlspecialchars($request->input('chargedFundsCenter'));

        $sortWay = 'asc';
        if($request->input('sortDesc') == 'true')
            $sortWay = 'desc';
        if($sortBy == null or $sortBy == 'null'){
            $sortBy = 'dueDate';
        }

        if($filter != 'null' && $filter != null){
            $query = SqmPayment::with(['fundsCenter'])->whereHas('fundsCenter', function($query) use ($filter) {
                $query->where('description', 'like', '%'.$filter.'%')
                    ->orwhere('startDate', 'like', '%'.$filter.'%')
                    ->orWhere('dueDate', 'like', '%'.$filter.'%')
                    ->orWhere('dueDate', 'like', '%'.$filter.'%')
                    ->orWhere('amount', 'like', '%'.$filter.'%');;});
        }else{
            $query = SqmPayment::with(['fundsCenter']);
        }

        if($chargedFundsCenter != 'null' && $chargedFundsCenter != null){
            $query->where('fundsCenter_id', '=', $chargedFundsCenter);
        }
        $query->orderBy($sortBy, $sortWay);
        if($perPage != 'null' and $perPage != null){
            $query->paginate($perPage);
            $lastPage = $query->paginate($perPage)->lastPage();
        }
        $result = $query->get();
        if($sum != 'null' && $sum != null){
            $funds = 0;
            foreach ($result as $value){
                    $funds = $funds + $value->amount;
            }
            $result = $funds;
        }

        return [$result, $lastPage ?? null];
    }

    public function update(SqmPayment $sqmPayment){
        $data = request()->validate([
            'startDate' => 'required|date',
            'dueDate' => 'required|date',
            'amount' => 'required|numeric',
            'fundsCenter' => 'required|numeric',
        ]);
        try {
            $sqmPayment->update([
                'fundsCenter_id' => $data['fundsCenter'],
                'startDate' => $data['startDate'],
                'dueDate' => $data['dueDate'],
                'amount' => $data['amount'],
            ]);
        }catch (QueryException $ex){
            return response()->json(null, 423);
        }

        return response()->json(null, 200);
    }

    public function delete(SqmPayment $sqmPayment){
        try {
            $sqmPayment->delete();
        } catch (\Exception $e) {
            return response()->json(null, 423);
        }
        return response()->json(null, 200);
    }
}
