<?php

namespace App\Http\Controllers;

use App\OneTimePayment;
use App\OngoingPayment;
use Illuminate\Http\Request;
use mikehaertl\pdftk\Pdf;

class OneTimePaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(){
        return view('oneTimePayment.create');
    }

    public function store(){
        $data = request()->validate([
            'claim_id' => 'required|numeric|unique:one_time_payments',
            'favoredFundsCenter_id' => 'required|numeric',
            'chargedFundsCenter_id' => 'required|numeric',
            'costType_id' => 'required|numeric',
            'grantedFunds' => 'required|numeric',
            'notes' => 'max:500',
            'requirements' => 'max:500',
            'dueDate' => 'required|date',
        ]);

        try {
            $oneTimePayment = OneTimePayment::create([
                'claim_id' => $data['claim_id'],
                'favoredFundsCenter_id' => $data['favoredFundsCenter_id'],
                'chargedFundsCenter_id' => $data['chargedFundsCenter_id'],
                'costType_id' => $data['costType_id'],
                'grantedFunds' => $data['grantedFunds'],
                'notes' => $data['notes'] ?? null,
                'requirements' => $data['requirements'] ?? null,
                'dueDate' => $data['dueDate'],
            ]);
            $oneTimePayment->save();
        }catch (QueryException $ex){
            return response()->json($ex->getMessage(), 200);
        }

        return response()->json(null, 200);
    }

    public function serveOneTimePayment(Request $request){
        $find = htmlspecialchars($request->input('find'));
        $filter = htmlspecialchars($request->input('filter'));
        $sortBy = htmlspecialchars($request->input('sortBy'));
        $perPage = htmlspecialchars($request->input('perPage'));
        $startDate = htmlspecialchars($request->input('startDate'));
        $endDate = htmlspecialchars($request->input('endDate'));
        $chargedFundsCenter = htmlspecialchars($request->input('chargedFundsCenter'));

        if($find != 'null' && $find != null){
            return OneTimePayment::with(['favoredFundsCenter','chargedFundsCenter', 'claim', 'costType'])->find($find);
        }

        $sortWay = 'asc';
        if($request->input('sortDesc') == 'true')
            $sortWay = 'desc';
        if($sortBy == null or $sortBy == 'null'){
            $sortBy = 'dueDate';
        }

        $query = OneTimePayment::with(['favoredFundsCenter','chargedFundsCenter', 'claim', 'costType']);
        if($filter != 'null' && $filter != null) {
            $query->where('grantedFunds', 'like', '%' . $filter . '%')
                ->orWhere('spentFunds', 'like', '%' . $filter . '%')
                ->orWhere('spentDate', 'like', '%' . $filter . '%')
                ->orWhere('notes', 'like', '%' . $filter . '%')
                ->orWhere('requirements', 'like', '%' . $filter . '%')
                ->orWhere('dueDate', 'like', '%' . $filter . '%');
        }
        if($chargedFundsCenter != 'null' && $chargedFundsCenter != null){
            $query->where('chargedFundsCenter_id', '=', $chargedFundsCenter);
        }

        if($startDate != 'null' && $startDate != null){
            if($endDate != 'null' && $endDate != null){
                $query->where('dueDate', '>=', $startDate);
                $query->where('dueDate', '<=', $endDate);
                $query->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('spentDate', '>=', $startDate)
                        ->where('spentDate', '<=', $endDate);
                });
            }else{
                $query->where('dueDate', '>=', $startDate)
                    ->orWhere('spentDate', '>=', $startDate);
            }
        }else if($endDate != 'null' && $endDate != null) {
            $query->where('dueDate', '<=', $endDate)
                ->orWhere('spentDate', '<=', $endDate);
        }

        if($perPage != 'null' and $perPage != null){
            $query->paginate($perPage);
            $lastPage = $query->paginate($perPage)->lastPage();
        }
        $result = $query->get();

        return [$result, $lastPage ?? null];
    }

    public function update(OneTimePayment $oneTimePayment){
        $data = request()->validate([
            'claim_id' => 'required|numeric',
            'favoredFundsCenter_id' => 'required|numeric',
            'chargedFundsCenter_id' => 'required|numeric',
            'costType_id' => 'required|numeric',
            'grantedFunds' => 'required|numeric',
            'spentFunds' => '',
            'notes' => 'max:500',
            'requirements' => 'max:500',
            'dueDate' => 'required|date',
            'spentDate' => ''
        ]);
        try {
            $oneTimePayment->update([
                'claim_id' => $data['claim_id'],
                'favoredFundsCenter_id' => $data['favoredFundsCenter_id'],
                'chargedFundsCenter_id' => $data['chargedFundsCenter_id'],
                'costType_id' => $data['costType_id'],
                'grantedFunds' => $data['grantedFunds'],
                'spentFunds' => $data['spentFunds'],
                'notes' => $data['notes'] ?? null,
                'requirements' => $data['requirements'] ?? null,
                'dueDate' => $data['dueDate'],
                'spentDate' => $data['spentDate'],
            ]);
        }catch (QueryException $ex){
            return response()->json($ex->getMessage(), 200);
        }

        return response()->json(null, 200);
    }

    public function show(OneTimePayment $oneTimePayment){
        return view('oneTimePayment.show', compact('oneTimePayment'));
    }

    public function createTransferFormular(OneTimePayment $oneTimePayment)
    {
        $filepath = storage_path('app/public/') . 'formularDocuments/AntragAufBudgetumbuchung.pdf';
        $pdf = new Pdf($filepath, [
            'command' => 'C:\Program Files (x86)\PDFtk Server\bin\pdftk.exe',
        ]);

        $date = explode('-', $oneTimePayment->claim->meeting->date);
        $germanDateFormat = $date[2].'.'.$date[1].'.'.$date[0];
        $lastTwoDigitsOfYear = substr($date[0], -2);
        $pdf->fillForm([
            'name' => auth()->user()->personalData->lastname . ', ' . auth()->user()->personalData->firstname,
            'number' => auth()->user()->personalData->number,
            'email' => auth()->user()->personalData->email,
            'amount' => $oneTimePayment->grantedFunds,
            'chargedFundsCenter' => $oneTimePayment->chargedFundsCenter->fundsCenterNumber,
            'chargedFonds' => $oneTimePayment->chargedFundsCenter->fond,
            'favoredFundsCenter' => $oneTimePayment->favoredFundsCenter->fundsCenterNumber,
            'favoredFonds' => $oneTimePayment->favoredFundsCenter->fond,
            'reason' =>    'SK II: ' . $germanDateFormat .
                ' TOP 2.1.' . $oneTimePayment->claim->ioa .
                ', SKII/' . $oneTimePayment->claim->printNumber . '/' . $lastTwoDigitsOfYear,
        ])->needAppearances()->send();
    }

    public function createBskFormular(OneTimePayment $oneTimePayment){
        $filepath = storage_path('app/public/') . 'formularDocuments/BeschlussDerStudienkommission.pdf';
        $pdf = new Pdf($filepath, [
            'command' => 'C:\Program Files (x86)\PDFtk Server\bin\pdftk.exe',
            // or on most Windows systems:
            // 'command' => 'C:\Program Files (x86)\PDFtk\bin\pdftk.exe',
            //'useExec' => true,  // May help on Windows systems if execution fails
        ]);

        $date = explode('-', $oneTimePayment->claim->meeting->date);
        $germanDateFormat = $date[2].'.'.$date[1].'.'.$date[0];
        $lastTwoDigitsOfYear = substr($date[0], -2);
        $pdf->fillForm([
            'title' => $oneTimePayment->claim->title,
            'meeting' =>    'SK II: ' . $germanDateFormat .
                ' TOP 2.1.' . $oneTimePayment->claim->ioa .
                ', SKII/' . $oneTimePayment->claim->printNumber . '/' . $lastTwoDigitsOfYear,
            'permit' => $oneTimePayment->grantedFunds,
            'requirements' => $oneTimePayment->requirements,
            'fundscenter' => $oneTimePayment->favoredFundsCenter->fundsCenterNumber,
            'costcenter' => $oneTimePayment->favoredFundsCenter->costCenter,
            'fonds' => $oneTimePayment->favoredFundsCenter->fond,
            'date' => date("d.m.y")
        ])->needAppearances()->send();
    }

    public function createVnFormular(OneTimePayment $oneTimePayment){
        $filepath = storage_path('app/public/') . 'formularDocuments/Verwendungsnachweis.pdf';
        $pdf = new Pdf($filepath, [
            'command' => 'C:\Program Files (x86)\PDFtk Server\bin\pdftk.exe',
            // or on most Windows systems:
            // 'command' => 'C:\Program Files (x86)\PDFtk\bin\pdftk.exe',
            //'useExec' => true,  // May help on Windows systems if execution fails
        ]);

        $date = explode('-', $oneTimePayment->claim->meeting->date);
        $germanDateFormat = $date[2].'.'.$date[1].'.'.$date[0];
        $lastTwoDigitsOfYear = substr($date[0], -2);
        $pdf->fillForm([
            'title' => $oneTimePayment->claim->title,
            'meeting' =>    'SK II: ' . $germanDateFormat .
                ' TOP 2.1.' . $oneTimePayment->claim->ioa .
                ', SKII/' . $oneTimePayment->claim->printNumber . '/' . $lastTwoDigitsOfYear,
            'start' => $germanDateFormat,
        ])->needAppearances()->send();
    }

    public function delete(OneTimePayment $oneTimePayment){
        if($oneTimePayment->proofOfUse == null){
            try {
                $oneTimePayment->delete();
            } catch (\Exception $e) {
                return response()->json(null, 423);
            }
            return response()->json(null, 200);
        }else{
            return response()->json(null, 412);
        }
    }
}
