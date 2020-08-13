<?php

namespace App\Http\Controllers;

use App\OneTimePayment;
use App\OngoingPayment;
use App\OngoingPaymentHistory;
use DateInterval;
use DatePeriod;
use DateTime;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use mikehaertl\pdftk\Pdf;
use Symfony\Component\VarDumper\Caster\DateCaster;

class OngoingPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('ongoingPayment.index');
    }

    public function show(OngoingPayment $ongoingPayment){
        return view('ongoingPayment.show', compact('ongoingPayment'));
    }

    public function create(){
        return view('ongoingPayment.create');
    }

    public function store(){
        $data = request()->validate([
            'claim_id' => 'required|numeric',
            'favoredFundsCenter_id' => 'required|numeric',
            'chargedFundsCenter_id' => 'required|numeric',
            'costType_id' => 'required|numeric',
            'grantedFunds' => 'required|numeric',
            'notes' => 'required|max:500',
            'plannedStartDate' => 'required|date',
            'plannedEndDate' => 'required|date',
            'due' => 'required',
            'requirements' => 'max:500',
            'christmasBonus' => ''
        ]);

        try {
            $ongoingPayment = OngoingPayment::create([
                'claim_id' => $data['claim_id'],
                'favoredFundsCenter_id' => $data['favoredFundsCenter_id'],
                'chargedFundsCenter_id' => $data['chargedFundsCenter_id'],
                'costType_id' => $data['costType_id'],
                'grantedFunds' => $data['grantedFunds'],
                'notes' => $data['notes'],
                'plannedStartDate' => $data['plannedStartDate'],
                'plannedEndDate' => $data['plannedEndDate'],
                'due' => $data['due'],
                'requirements' => $data['requirements'],
                'christmasBonus' => $data['christmasBonus'] ?? null
            ]);
            $ongoingPayment->save();
            $this->createPaymentHistories($data, $ongoingPayment);
        }catch (QueryException $ex){
            return response()->json($ex->getMessage(), 200);
        }

        return response()->json(null, 200);
    }

    public function update(OngoingPayment $ongoingPayment){
        $data = request()->validate([
            'claim_id' => 'required|numeric',
            'favoredFundsCenter_id' => 'required|numeric',
            'chargedFundsCenter_id' => 'required|numeric',
            'costType_id' => 'required|numeric',
            'grantedFunds' => 'required|numeric',
            'notes' => 'required|max:500',
            'plannedStartDate' => 'required|date',
            'plannedEndDate' => 'required|date',
            'due' => 'required',
            'requirements' => 'max:500',
            'christmasBonus' => ''
        ]);
        try {
            $ongoingPayment->update([
                'claim_id' => $data['claim_id'],
                'favoredFundsCenter_id' => $data['favoredFundsCenter_id'],
                'chargedFundsCenter_id' => $data['chargedFundsCenter_id'],
                'costType_id' => $data['costType_id'],
                'grantedFunds' => $data['grantedFunds'],
                'notes' => $data['notes'],
                'plannedStartDate' => $data['plannedStartDate'],
                'plannedEndDate' => $data['plannedEndDate'],
                'due' => $data['due'],
                'requirements' => $data['requirements'],
                'christmasBonus' => $data['christmasBonus'] ?? null
            ]);
            $this->deletePlannedPaymentHistories($data, $ongoingPayment);

        }catch (QueryException $ex){
            return response()->json($ex->getMessage(), 200);
        }

        return response()->json(null, 200);
    }

    private function createPaymentHistories($data, $ongoingPayment){
        // ToDo: Weihnachtsgeldberechnung einfügen

        $startDate = new DateTime($data['plannedStartDate']);
        $endDate = new DateTime($data['plannedEndDate']);

        $due = $data['due'];
        if($due == 'monthly'){
            $interval = DateInterval::createFromDateString('1 months');
            $startDate = $startDate->modify('first day of this month');
            $endDate = $endDate->modify('first day of next month');
            $period = new DatePeriod($startDate, $interval, $endDate);

        }else if($due == 'halfyearly'){
                $interval = DateInterval::createFromDateString('6 months');
                $period = new DatePeriod($startDate, $interval, $endDate);
        }else if($due == 'yearly'){
                $interval = DateInterval::createFromDateString('1 year');
                $endDate = $endDate->modify( '+1 day' );
                $period = new DatePeriod($startDate, $interval, $endDate);
        }
        foreach ($period as $dt) {
            $plannedPaymentDate = ($dt->format("Y-m-d"));
            $ongoingPayment->ongoingPaymentHistories()->create([
                'claim_id' => $data['claim_id'],
                'chargedFundsCenter_id' => $data['chargedFundsCenter_id'],
                'grantedFunds' => $data['grantedFunds'],
                'plannedPaymentDate' => $plannedPaymentDate,
            ]);
            $dontCalculateChristmasBonus = $data['christmasBonus'] ?? false;
            if($dt->format("n") == 10 && $due == 'monthly' && !$dontCalculateChristmasBonus){
                    $ongoingPayment->ongoingPaymentHistories()->create([
                        'claim_id' => $data['claim_id'],
                        'chargedFundsCenter_id' => $data['chargedFundsCenter_id'],
                        'grantedFunds' => $data['grantedFunds'],
                        'plannedPaymentDate' => $plannedPaymentDate,
                    ]);
                }
            }
    }

    public function deletePlannedPaymentHistories($data, $ongoingPayment){
        $ongoingPayment->ongoingPaymentHistories()->where('spentFunds', "=", null)->delete();
        $this->createPaymentHistories($data, $ongoingPayment);
    }

    public function serveOngoingPayment(Request $request){
        $find = htmlspecialchars($request->input('find'));
        $filter = htmlspecialchars($request->input('filter'));
        $sortBy = htmlspecialchars($request->input('sortBy'));
        $perPage = htmlspecialchars($request->input('perPage'));
        $startDate = htmlspecialchars($request->input('startDate'));
        $endDate = htmlspecialchars($request->input('endDate'));

        if($find != 'null' && $find != null){
            return OngoingPayment::with(['favoredFundsCenter','chargedFundsCenter', 'claim', 'costType'])->find($find);
        }

        $sortWay = 'asc';
        if($request->input('sortDesc') == 'true')
            $sortWay = 'desc';
        if($sortBy == null or $sortBy == 'null'){
            $sortBy = 'plannedEndDate';
        }


        $query = OngoingPayment::with(['favoredFundsCenter','chargedFundsCenter', 'claim', 'costType']);
        if($filter != 'null' && $filter != null) {
            $query->where('grantedFunds', 'like', '%' . $filter . '%')
                ->orWhere('notes', 'like', '%' . $filter . '%')
                ->orWhere('requirements', 'like', '%' . $filter . '%');
        }
        if($startDate != 'null' && $startDate != null)
            $query->where('plannedStartDate', '>=', $startDate);
        if($endDate != 'null' && $endDate != null)
            $query->where('plannedEndDate', '<=', $endDate);
        $query->orderBy($sortBy, $sortWay);
        if($perPage != 'null' and $perPage != null){
            $query->paginate($perPage);
            $lastPage = $query->paginate($perPage)->lastPage();
        }
        $result = $query->get();

        return [$result, $lastPage ?? null];
    }

    public function createTransferFormular(Request $request)
    {
        $ongoingPayment = OngoingPayment::find(htmlspecialchars($request->input('id')));
        $startDate = htmlspecialchars($request->input('startDate'));
        $endDate = htmlspecialchars($request->input('endDate'));

        if($startDate == 'null' || $endDate == 'null'){
            return('Sie müssen ein Start und ein Enddatum auswählen');
        }

        $filepath = storage_path('app/public/') . 'formularDocuments/AntragAufBudgetumbuchung.pdf';
        $pdf = new Pdf($filepath, [
            'command' => 'C:\Program Files (x86)\PDFtk Server\bin\pdftk.exe',
            // or on most Windows systems:
            // 'command' => 'C:\Program Files (x86)\PDFtk\bin\pdftk.exe',
            //'useExec' => true,  // May help on Windows systems if execution fails
        ]);

        $date = explode('-', $ongoingPayment->claim->meeting->date);
        $germanDateFormat = $date[2].'.'.$date[1].'.'.$date[0];
        $lastTwoDigitsOfYear = substr($date[0], -2);
        $pdf->fillForm([
            'name' => auth()->user()->personalData->lastname . ', ' . auth()->user()->personalData->firstname,
            'number' => auth()->user()->personalData->number,
            'email' => auth()->user()->personalData->email,
            'amount' => $ongoingPayment->ongoingPaymentHistories()->where('plannedPaymentDate', ">=", $startDate)
                ->where('plannedPaymentDate', "<=", $endDate)->sum('grantedFunds'),
            'chargedFundsCenter' => $ongoingPayment->chargedFundsCenter->fundsCenterNumber,
            'chargedFonds' => $ongoingPayment->chargedFundsCenter->fond,
            'favoredFundsCenter' => $ongoingPayment->favoredFundsCenter->fundsCenterNumber,
            'favoredFonds' => $ongoingPayment->favoredFundsCenter->fond,
            'reason' => 'SK II: ' . $germanDateFormat .
                ' TOP 2.1.' . $ongoingPayment->claim->ioa .
                ', SKII/' . $ongoingPayment->claim->printNumber . '/' . $lastTwoDigitsOfYear,
        ])->needAppearances()->send();
    }

    public function createBskFormular(Request $request){
        $ongoingPayment = OngoingPayment::find(htmlspecialchars($request->input('id')));
        $startDate = htmlspecialchars($request->input('startDate'));
        $endDate = htmlspecialchars($request->input('endDate'));

        if($startDate == 'null' || $endDate == 'null'){
            return('Sie müssen ein Start und ein Enddatum auswählen');
        }

        $filepath = storage_path('app/public/') . 'formularDocuments/BeschlussDerStudienkommission.pdf';
        $pdf = new Pdf($filepath, [
            'command' => 'C:\Program Files (x86)\PDFtk Server\bin\pdftk.exe',
            // or on most Windows systems:
            // 'command' => 'C:\Program Files (x86)\PDFtk\bin\pdftk.exe',
            //'useExec' => true,  // May help on Windows systems if execution fails
        ]);


        $date = explode('-', $ongoingPayment->claim->meeting->date);
        $germanDateFormat = $date[2].'.'.$date[1].'.'.$date[0];
        $lastTwoDigitsOfYear = substr($date[0], -2);
        $pdf->fillForm([
            'title' => $ongoingPayment->claim->title,
            'meeting' =>    'SK II: ' . $germanDateFormat .
                ' TOP 2.1.' . $ongoingPayment->claim->ioa .
                ', SKII/' . $ongoingPayment->claim->printNumber . '/' . $lastTwoDigitsOfYear,
            'permit' => $ongoingPayment->ongoingPaymentHistories()->where('plannedPaymentDate', ">=", $startDate)
                ->where('plannedPaymentDate', "<=", $endDate)->sum('grantedFunds'),
            'requirements' => '',
            'fundscenter' => $ongoingPayment->chargedFundsCenter->fundsCenterNumber,
            'costcenter' => $ongoingPayment->chargedFundsCenter->costCenter,
            'fonds' => $ongoingPayment->chargedFundsCenter->fond,
            'date' => date("d.m.y")
        ])->needAppearances()->send();
    }

    public function createVnFormular(OngoingPayment $ongoingPayment){
        $filepath = storage_path('app/public/') . 'formularDocuments/Verwendungsnachweis.pdf';
        $pdf = new Pdf($filepath, [
            'command' => 'C:\Program Files (x86)\PDFtk Server\bin\pdftk.exe',
            // or on most Windows systems:
            // 'command' => 'C:\Program Files (x86)\PDFtk\bin\pdftk.exe',
            //'useExec' => true,  // May help on Windows systems if execution fails
        ]);

        $date = explode('-', $ongoingPayment->claim->meeting->date);
        $germanDateFormat = $date[2].'.'.$date[1].'.'.$date[0];
        $lastTwoDigitsOfYear = substr($date[0], -2);
        $pdf->fillForm([
            'title' => $ongoingPayment->claim->title,
            'meeting' =>    'SK II: ' . $germanDateFormat .
                ' TOP 2.1.' . $ongoingPayment->claim->ioa .
                ', SKII/' . $ongoingPayment->claim->printNumber . '/' . $lastTwoDigitsOfYear,
            'start' => $germanDateFormat,
        ])->needAppearances()->send();
    }

}
