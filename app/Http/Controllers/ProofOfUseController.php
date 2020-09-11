<?php

namespace App\Http\Controllers;

use App\ProofOfUse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProofOfUseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(ProofOfUse $proofOfUse){
        return view('proofOfUse.show', compact('proofOfUse'));
    }

    public function store(){

        $data = request()->validate([
            'unique_payment_id' => 'required',
            'submitter' => 'required',
            'submitDate' => 'required|date',
            'document' => 'required|mimes:pdf',
        ]);

        $upload_path = request()->document->store('proofOfUseDocuments', 'public');

        try {
            $proofOfUse = ProofOfUse::create([
                'grantedClaim_id' => $data['unique_payment_id'],
                'submitter' => $data['submitter'],
                'submitDate' => $data['submitDate'],
                'document' => $upload_path,
            ]);
            $proofOfUse->save();
        }catch (QueryException $ex){
            return response()->json(null, 423);
        }

        return response()->json(null, 200);
    }

    public function serveProofOfUses(Request $request){
        $unique_payment_id = htmlspecialchars($request->input('find'));

        if($unique_payment_id != 'null' && $unique_payment_id != null){
            return ProofOfUse::Where('grantedClaim_id','=', $unique_payment_id)->first();
        }
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

    public function update(ProofOfUse $proofOfUse){

        $data = request()->validate([
            'unique_payment_id' => 'required',
            'submitter' => 'required',
            'submitDate' => 'required|date',
        ]);

        try {
            $proofOfUse->update([
                'grantedClaim' => $data['unique_payment_id'],
                'submitter' => $data['submitter'],
                'submitDate' => $data['submitDate'],
            ]);
        }catch (QueryException $ex){
            return response()->json(null, 423);
        }

        return response()->json(null, 200);
    }

    public function delete(ProofOfUse $proofOfUse){
        try {
            Storage::disk('public')->delete($proofOfUse->document);
            $proofOfUse->delete();
            } catch (\Exception $e) {
                return response()->json(null, 423);
            }
            return response()->json(null, 200);
    }
}
