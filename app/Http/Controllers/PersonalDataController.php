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

    public function show(User $user)
    {
        return view('personalData.show', compact('user'));
    }


    public function edit(User $user){
        $this->authorize('update', $user->personalData);
        return view('personalData.edit', compact('user'));
    }

    public function update(User $user){
        $this->authorize('update',$user->personalData);
        $data = request()->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'number' => '',
        ]);
        auth()->user()->personalData()->update($data);

        return redirect('/personalData/'.auth()->user()->id);
    }
}
