<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'required'
        ]);
        $exists = User::where('email', $request->email)->exists();
        if($exists){
            return response()->json(['error' => 'You are already register'], 409);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name
        ]);

        $token = $user->createToken($request->email)->plainTextToken;

        return response()->json([
            'token' => $token,
            'email' => $user->email,
            'name' => $user->name,
            'created_at' => $user->created_at
        ], 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                    "errors" => 'The provided credentials are incorrect.'
                ], 401);
        }
        
        $user->tokens()->where('tokenable_id', $user->id)->delete();

        $token = $user->createToken($request->email)->plainTextToken;

        return response()->json([
            'token' => $token,
            'email' => $user->email,
            'name' => $user->name,
            'created_at' => $user->created_at
        ], 200);
    
    }
    
    public function me(Request $request)
    {
        return response()->json([
            'id' => $request->user()->id,
            'email' => $request->user()->email,
            'name' => $request->user()->name,
            'created_at' => $request->user()->created_at,
            'updated_at' => $request->user()->update_at
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response(null, 204);
    }

}
