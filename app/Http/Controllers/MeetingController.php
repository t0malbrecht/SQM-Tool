<?php

namespace App\Http\Controllers;

use App\Claim;
use App\Exports\MeetingExport;
use App\Meeting;
use App\OneTimePayment;
use App\OngoingPayment;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class MeetingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){

        return view('meetings.index');
    }

    public function serveMeetings(Request $request){
        $filter = htmlspecialchars($request->input('filter'));
        $sortBy = htmlspecialchars($request->input('sortBy'));
        $perPage = htmlspecialchars($request->input('perPage'));
        $startDate = htmlspecialchars($request->input('startDate'));
        $endDate = htmlspecialchars($request->input('endDate'));

        $sortWay = 'asc';
        if($request->input('sortDesc') == 'true')
            $sortWay = 'desc';


        $query = Meeting::Select(['id','date']);
        if($filter != 'null' && $filter != null) {
            $query->where('date', 'like', '%' . $filter . '%');
        }
        if($startDate != 'null' && $startDate != null)
            $query->where('date', '>=', $startDate);
        if($endDate != 'null' && $endDate != null)
            $query->where('date', '<=', $endDate);
        if($perPage != 'null' && $perPage != null){
            $query->paginate($perPage);
        }

        $query->orderBy($sortBy, $sortWay);
        $result = $query->get();
        $lastPage = $query->paginate($perPage)->lastPage();

        return [$result, $lastPage];
    }

    public function create(){
        return view('meetings.create');
    }

    public function store(){
        $data = request()->validate([
            'date' => 'required|date',
        ]);

        try{
            $meeting =  Meeting::create([
                'date' => $data['date'],
            ]);

            $meeting->save();
        }catch(QueryException $e){
            return response()->json(null, 423);
        }

        return response()->json(null, 200);
    }

    public function update(Meeting $meeting){

        $data = request()->validate([
            'date' => 'required|date',
        ]);

        try{
        $meeting->update([
            'date' => $data['date'],
        ]);
        }catch(QueryException $e){
            return response()->json(null, 422);
        }
        return response()->json(null, 200);
    }

    public function show(Meeting $meeting){
        return view('meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting){
        return view('meetings.edit', compact('meeting'));
    }

    public function delete(Meeting $meeting){
        if(sizeof($meeting->claims) == 0){
            try {
                $meeting->delete();
            } catch (\Exception $e) {
                return response()->json(null, 423);
            }
            return response()->json(null, 200);
        }else{
            return response()->json(null, 412);
        }
    }

    public function exportXlsx(Meeting $meeting)
    {
        return Excel::download(new MeetingExport($meeting), 'Sitzung_'.$meeting->date.'_.xlsx');
    }
}
