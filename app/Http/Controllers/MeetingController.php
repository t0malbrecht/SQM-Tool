<?php

namespace App\Http\Controllers;

use App\Meeting;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

        $query = Meeting::Select(['id','date', 'number', 'yearNumberCombined']);
        if($filter != 'null' && $filter != null)
            $query->where('number', 'like', '%'.$filter.'%');
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
        $date = request()->input('date');
        $year = explode('-', $date);

        $data = request()->validate([
            'date' => 'required|date',
            'number' => 'required|numeric',
            $year[0].'-'.'number' => 'unique:App\Meeting,yearNumberCombined'
        ]);

        try{
            $meeting =  Meeting::create([
                'date' => $data['date'],
                'number' => $data['number'],
                'yearNumberCombined' => $year[0].'-'.$data['number'],
            ]);

            $meeting->save();
        }catch(QueryException $e){
            return response()->json(null, 433);
        }

        return response()->json(null, 200);
    }

    public function update(Meeting $meeting){

        $data = request()->validate([
            'date' => 'required|date',
            'number' => 'required|numeric',
            'yearNumberCombined' => ''
        ]);

        try{
        $meeting->update([
            'date' => $data['date'],
            'number' => $data['number'],
            'yearNumberCombined' => $data['yearNumberCombined']]);
        }catch(QueryException $e){
            return response()->json(null, 433);
        }

        return response()->json(null, 200);
    }

    public function show(Meeting $meeting){
        return view('meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting){
        return view('meetings.edit', compact('meeting'));
    }
}
