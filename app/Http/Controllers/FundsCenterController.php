<?php

namespace App\Http\Controllers;

use App\FundsCenter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class FundsCenterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('fundsCenter.index');
    }

    public function serveFundCenters(Request $request){
        $level = htmlspecialchars($request->input('level'));
        $filter = htmlspecialchars($request->input('filter'));
        $sortBy = htmlspecialchars($request->input('sortBy'));
        $perPage = htmlspecialchars($request->input('perPage'));

        $sortWay = 'asc';
        if($request->input('sortDesc') == 'true')
            $sortWay = 'desc';
        if($sortBy == null or $sortBy == 'null'){
            $sortBy = 'division';
        }

        $query = FundsCenter::Select(['id','fond', 'teachingUnit','level', 'division', 'description', 'faculty', 'professor', 'fundsCenterNumber', 'costCenter']);
        if($filter != 'null' && $filter != null) {
            $query->where('division', 'like', '%' . $filter . '%')
                ->orWhere('description', 'like', '%' . $filter . '%')
                ->orWhere('fundsCenterNumber', 'like', '%' . $filter . '%')
                ->orWhere('costCenter', 'like', '%' . $filter . '%')
                ->orWhere('fond', 'like', '%' . $filter . '%')
                ->orWhere('teachingUnit', 'like', '%' . $filter . '%')
                ->orWhere('faculty', 'like', '%' . $filter . '%')
                ->orWhere('professor', 'like', '%' . $filter . '%');
        }
        if($level != 'null' and $level != null){
            $query->where('level', '=', $level);
        }
        $query->orderBy($sortBy, $sortWay);
        if($perPage != 'null' and $perPage != null){
            $query->paginate($perPage);
            $lastPage = $query->paginate($perPage)->lastPage();
        }
        $result = $query->get();

        return [$result, $lastPage ?? null];
    }

    public function create(){
        return view('fundsCenter.create');
    }

    public function store(){
        $data = request()->validate([
            'division' => 'required',
            'description' => 'required|unique:App\FundsCenter,description',
            'fundsCenterNumber' => 'required|numeric|unique:App\FundsCenter,fundsCenterNumber',
            'costCenter' => 'numeric|unique:App\FundsCenter,costCenter',
            'fond' => 'required',
            'teachingUnit' => 'required|numeric',
            'faculty' => 'required',
            'professor' => '',
            'level' => 'required|numeric',
        ]);
        try {
            $fundsCenter = FundsCenter::create([
                'division' => $data['division'],
                'description' => $data['description'],
                'fundsCenterNumber' => $data['fundsCenterNumber'],
                'costCenter' => $data['costCenter'] ?? null,
                'fond' => $data['fond'],
                'teachingUnit' => $data['teachingUnit'],
                'faculty' => $data['faculty'],
                'professor' => $data['professor'] ?? null,
                'level' => $data['level'],
            ]);
            $fundsCenter->save();
        }catch (QueryException $ex){
            return response()->json($ex->getMessage(), 200);
        }

        return response()->json(null, 200);
    }

    public function update(FundsCenter $fundsCenter){
        $data = request()->validate([
            'division' => 'required',
            'description' => 'required',
            'fundsCenterNumber' => 'required',
            'costCenter' => 'numeric',
            'fond' => 'required',
            'teachingUnit' => 'required|numeric',
            'faculty' => 'required',
            'professor' => '',
            'level' => 'required|numeric',
        ]);
        try {
            $fundsCenter->update([
                'division' => $data['division'],
                'description' => $data['description'],
                'fundsCenterNumber' => $data['fundsCenterNumber'],
                'costCenter' => $data['costCenter'] ?? null,
                'fond' => $data['fond'],
                'teachingUnit' => $data['teachingUnit'],
                'faculty' => $data['faculty'],
                'professor' => $data['professor'] ?? null,
                'level' => $data['level'],
            ]);
        }catch (QueryException $ex){
            return response()->json($ex->getMessage(), 200);
        }

        return response()->json(null, 200);
    }

    public function show(FundsCenter $fundsCenter){
        return view('fundsCenter.show', compact('meeting'));
    }

    public function edit(FundsCenter $fundsCenter){
        return view('fundsCenter.edit', compact('meeting'));
    }
}
