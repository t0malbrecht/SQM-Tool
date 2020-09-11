<?php

namespace App\Http\Controllers;

use App\PersonalData;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PersonalDataController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(PersonalData $personalData)
    {
        return view('personalData.show', compact('personalData'));
    }


    public function edit(PersonalData $personalData){
        return view('personalData.edit', compact('personalData'));
    }

    public function update(PersonalData $personalData){
        $data = request()->validate([
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'number' => '',
        ]);
        try {
            $personalData->update([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'number' => $data['number'],
            ]);
        }catch (QueryException $ex){
            return response()->json(null, 423);
        }

        return redirect('/personalData/'.$personalData->id);
    }
}
