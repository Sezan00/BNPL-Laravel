<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MerchantController extends Controller
{
    public function register(Request $request){
         $request->validate([
            'merchant_name' => 'required|max:30',
            'email' => 'required|email|unique:merchants,email',
            'document_id' => 'nullable|exists:documents,id',
            'phone' => 'nullable|string|max:20|unique:merchants,phone',
            'business_name' => 'required|max:40',
            'password' => 'required|min:1',
        ]);

        $Merchant = Merchant::create([
            'merchant_name' => $request->merchant_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'business_name' => $request->business_name,
            'password'      => Hash::make($request->password),
        ]);

        // logger('merchant create', $Merchant->toArray());

        return response()->json([
            'message' => 'merchant account created',
            'merchant' => $Merchant
        ], 200);
    }

    public function login(Request $request){
      $validate = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $merchant = Merchant::where('email', $validate['email'])->first();

        if(! $merchant || ! Hash::check($validate['password'], $merchant->password)){
             throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]); 
        }

        $token = $merchant->createToken('merchant-token')->plainTextToken;

        return response()->json([
            'merchant' => $merchant,
            'token' => $token
        ]);
    }

    public function merchantLogout(Request $request){
        $merchant = auth('merchant')->user();

        if ($merchant) {
            /** @var PersonalAccessToken|null $token */
            $token = $merchant->currentAccessToken();

            if ($token) {
                $token->delete();
            }
        }

        return response()->json([
            'message' => 'Merchant logout successful'
        ], 200);
    }
}
