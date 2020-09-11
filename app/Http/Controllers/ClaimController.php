<?php

namespace App\Http\Controllers;

use App\Claim;
use App\OneTimePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClaimController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(){

        $data = request()->validate([
            'meeting_id' => 'required|numeric',
            'title' => 'required|max:255',
            'ioa' => 'required|numeric',
            'printNumber' => 'required|numeric',
            'claimant' => 'required',
            'document' => 'required|mimes:pdf',
            'decided' => ''
        ]);

        $data['decided'] = $data['decided'] ?? false;

        if($data['decided'] == 'true' ?? $data['decided'] = false){
            $data['decided'] = true;
        }else{
            $data['decided'] = false;
        }


        $upload_path = request()->document->store('claimDocuments', 'public');

        try {
            $claim = Claim::create([
                'title' => $data['title'],
                'ioa' => $data['ioa'],
                'printNumber' => $data['printNumber'],
                'claimant' => $data['claimant'],
                'meeting_id' => $data['meeting_id'],
                'document' => $upload_path,
                'decided' => $data['decided'] ?? false
            ]);
            $claim->save();
        }catch (QueryException $ex){
            return response()->json(null, 423);
        }

        return response()->json(null, 200);
    }

    public function serveClaims(Request $request){
        $find = htmlspecialchars($request->input('find'));
        $filter = htmlspecialchars($request->input('filter'));
        $sortBy = htmlspecialchars($request->input('sortBy'));
        $perPage = htmlspecialchars($request->input('perPage'));
        $id = htmlspecialchars($request->input('id'));

        if($find != 'null' && $find != null){
            return Claim::Find($find);
        }


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
            'title' => 'required|max:255',
            'ioa' => 'required',
            'printNumber' => 'required|numeric',
            'claimant' => 'required',
            'decided' => ''
        ]);

        if($data['decided'] == 'true'){
            $data['decided'] = true;
        }else{
            $data['decided'] = false;
        }

        try{
            $claim->update([
                'title' => $data['title'],
                'ioa' => $data['ioa'],
                'printNumber' => $data['printNumber'],
                'claimant' => $data['claimant'],
                'meeting_id' => $data['meeting_id'],
                'decided' => $data['decided']
            ]);
        }catch(QueryException $e){
            return response()->json(null, 423);
        }

        return response()->json(null, 200);
    }

    public function delete(Claim $claim){
        if($claim->oneTimePayment == null && $claim->ongoingPayment == null){
            try {
                Storage::disk('public')->delete($claim->document);
                $claim->delete();
            } catch (\Exception $e) {
                return response()->json(null, 423);
            }
            return response()->json(null, 200);
        }else{
            return response()->json(null, 412);
        }
    }
}
