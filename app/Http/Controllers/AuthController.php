<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function registerUser(Request $request)
    {
        $request->validate([
            'name' => 'required|max:30',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|min:1',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
        ]);

        return response()->json([
            'message' => 'user register successfully'
        ], 200);
    }

    public function loginUser(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validate['email'])->first();

        if (! $user || ! Hash::check($validate['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request)
{
    $user = $request->user();

    if ($user) {
        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }
    }

    return response()->json([
        'message' => 'Logout successful'
    ], 200);
}

    public function index(){
        $user = User::all();

        return response()->json(['user' => $user]);
    }
}
