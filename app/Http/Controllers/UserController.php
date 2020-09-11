<?php

namespace App\Http\Controllers;

use App\PersonalData;
use App\Policies\UserPolicy;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        return view('user.index');
    }


    public function store(Request $request)
    {
        $data = request()->validate([
            'username' => ['required', 'string', 'max:255'. 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'numeric'],
        ]);

        try {
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
            ]);

            PersonalData::create([
                'email' => $data['email'],
                'user_id' => $user->id,
            ]);

        }catch (QueryException $ex){
            return response()->json(null, 423);
        }

        return response()->json(null, 200);
    }

    public function serveUsers(Request $request){
        $perPage = htmlspecialchars($request->input('perPage'));

        if($perPage != 'null' and $perPage != null){
            $result = User::paginate($perPage);
        }else{
            $result = User::all();
        }

        return [$result, $lastPage ?? null];
    }

    public function update(User $user)
    {
        $data = request()->validate([
            'username' => ['required', 'string', 'max:255'. 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['string', 'min:8'],
            'role' => ['required', 'numeric'],
        ]);

        try {
            $user->update([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
            ]);
        }catch (QueryException $ex){
            return response()->json(null, 423);
        }

        return response()->json(null, 200);
    }

    public function delete(User $user){
        try {
            $user->delete();
        } catch (\Exception $e) {
            return response()->json(null, 423);
        }
        return response()->json(null, 200);
    }
}
