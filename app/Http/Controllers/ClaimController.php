<?php

namespace App\Http\Controllers;

use App\Claim;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(){

        $data = request()->validate([
            'meeting_id' => 'required|numeric',
            'title' => 'required',
            'ioa' => 'required',
            'printNumber' => 'required|numeric',
            'claimant' => 'required',
            'document' => 'required'
        ]);

        $upload_path = request()->document->store('claimDocuments', 'public');

        try {
            $claim = Claim::create([
                'title' => $data['title'],
                'ioa' => $data['ioa'],
                'printNumber' => $data['printNumber'],
                'claimant' => $data['claimant'],
                'meeting_id' => $data['meeting_id'],
                'document' => $upload_path,
            ]);
            $claim->save();
        }catch (QueryException $ex){
            return response()->json(null, 200);
        }

        return response()->json(null, 200);
    }

    public function serveClaims(Request $request){
        //ToDO: Filter nach Anträgen ohne zugeordnetem grantedClaim für Zahlungen erstellen (außer Zahlung erstellen nur über Antrag möglich)

        $filter = htmlspecialchars($request->input('filter'));
        $sortBy = htmlspecialchars($request->input('sortBy'));
        $perPage = htmlspecialchars($request->input('perPage'));
        $id = htmlspecialchars($request->input('id'));

        $sortWay = 'asc';
        if($request->input('sortDesc') == 'true')
            $sortWay = 'desc';
        if($sortBy == null or $sortBy == 'null'){
            $sortBy = 'ioa';
        }

        $query = Claim::with('meeting', 'oneTimePayment', 'ongoingPayment');
        if($id != 'null' && $id != null)
            $query->where('meeting_id' , '=', $id);
        if($filter != 'null' && $filter != null)
            $query->where('title', 'like', '%'.$filter.'%')
                ->orWhere('ioa', 'like', '%'.$filter.'%')
                ->orWhere('claimant', 'like', '%'.$filter.'%');
        $query->orderBy($sortBy, $sortWay);
        if($perPage != 'null' and $perPage != null){
            $query->paginate($perPage);
            $lastPage = $query->paginate($perPage)->lastPage();
        }
        $result = $query->get();

        return [$result, $lastPage ?? null];
    }

    public function showDocument(Request $request)
    {
        $file = storage_path('app/public/') . $request->input('path');

        if (file_exists($file)) {

            $headers = [
                'Content-Type' => 'application/pdf'
            ];

            return response()->download($file, 'Test File', $headers, 'inline');
        } else {
            abort(404, 'File not found!');
        }
    }

    public function update(Claim $claim){

        $data = request()->validate([
            'meeting_id' => 'required|numeric',
            'title' => 'required',
            'ioa' => 'required',
            'printNumber' => 'required|numeric',
            'claimant' => 'required'
        ]);

        try{
            $claim->update([
                'title' => $data['title'],
                'ioa' => $data['ioa'],
                'printNumber' => $data['printNumber'],
                'claimant' => $data['claimant'],
                'meeting_id' => $data['meeting_id'],
            ]);
        }catch(QueryException $e){
            return response()->json(null, 433);
        }

        return response()->json(null, 200);
    }
}
