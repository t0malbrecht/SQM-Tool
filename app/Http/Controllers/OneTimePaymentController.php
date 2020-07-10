<?php

namespace App\Http\Controllers;

use App\OneTimePayment;
use Illuminate\Http\Request;
use mikehaertl\pdftk\Pdf;

class OneTimePaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('oneTimePayment.index');
    }

    public function create(){
        return view('oneTimePayment.create');
    }

    public function store(){
        $data = request()->validate([
            'claim_id' => 'required|numeric',
            'favoredFundsCenter_id' => 'required|numeric',
            'chargedFundsCenter_id' => 'required|numeric',
            'costType_id' => 'required|numeric',
            'grantedFunds' => 'required|numeric',
            'description' => 'required',
            'requirements' => 'required',
            'dueDate' => 'required|date',
        ]);

        try {
            $oneTimePayment = OneTimePayment::create([
                'claim_id' => $data['claim_id'],
                'favoredFundsCenter_id' => $data['favoredFundsCenter_id'],
                'chargedFundsCenter_id' => $data['chargedFundsCenter_id'],
                'costType_id' => $data['costType_id'],
                'grantedFunds' => $data['grantedFunds'],
                'description' => $data['description'],
                'requirements' => $data['requirements'],
                'dueDate' => $data['dueDate'],
            ]);
            $oneTimePayment->save();
        }catch (QueryException $ex){
            return response()->json($ex->getMessage(), 200);
        }

        return response()->json(null, 200);
    }

    public function serveOneTimePayment(Request $request){
        $filter = htmlspecialchars($request->input('filter'));
        $sortBy = htmlspecialchars($request->input('sortBy'));
        $perPage = htmlspecialchars($request->input('perPage'));
        $startDate = htmlspecialchars($request->input('startDate'));
        $endDate = htmlspecialchars($request->input('endDate'));

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
                ->orWhere('description', 'like', '%' . $filter . '%')
                ->orWhere('requirements', 'like', '%' . $filter . '%')
                ->orWhere('dueDate', 'like', '%' . $filter . '%');
        }
        if($startDate != 'null' && $startDate != null)
            $query->where('dueDate', '>=', $startDate);
        if($endDate != 'null' && $endDate != null)
            $query->where('dueDate', '<=', $endDate);
        $query->orderBy($sortBy, $sortWay);
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
            'description' => 'required',
            'requirements' => 'required',
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
                'description' => $data['description'],
                'requirements' => $data['requirements'],
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
            // or on most Windows systems:
            // 'command' => 'C:\Program Files (x86)\PDFtk\bin\pdftk.exe',
            //'useExec' => true,  // May help on Windows systems if execution fails
        ]);

        $pdf->fillForm([
            'name' => auth()->user()->personalData->lastname . ', ' . auth()->user()->personalData->firstname,
            'number' => auth()->user()->personalData->number,
            'email' => auth()->user()->personalData->email,
            'amount' => $oneTimePayment->grantedFunds,
            'chargedFundsCenter' => $oneTimePayment->chargedFundsCenter->fundsCenterNumber,
            'chargedFonds' => $oneTimePayment->chargedFundsCenter->fond,
            'favoredFundsCenter' => $oneTimePayment->favoredFundsCenter->fundsCenterNumber,
            'favoredFonds' => $oneTimePayment->favoredFundsCenter->fond,
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

        $year = explode('-', $oneTimePayment->claim->date)[0];
        //$year = ''.$year;
        $lastTwoDigitsOfYear = substr($year, -2);
        $pdf->fillForm([
            'title' => $oneTimePayment->claim->title,
            'meeting' =>    'SK II:' . $oneTimePayment->claim->meeting->date .
                ' TOP ' . $oneTimePayment->claim->ioa .
                ', SKII/' . $oneTimePayment->claim->printNumber . '/' . $lastTwoDigitsOfYear,
            'permit' => $oneTimePayment->grantedFunds,
            'requirements' => $oneTimePayment->requirements,
            'fundscenter' => $oneTimePayment->chargedFundsCenter->fundsCenterNumber,
            'costcenter' => $oneTimePayment->chargedFundsCenter->costCenter,
            'fonds' => $oneTimePayment->chargedFundsCenter->fond,
            'date' => date("d.m.y")
        ])->needAppearances()->send();
    }
}
