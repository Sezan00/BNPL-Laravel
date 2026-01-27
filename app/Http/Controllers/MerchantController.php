<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        logger('merchant create', $Merchant->toArray());

        return response()->json([
            'message' => 'merchant account created',
            'merchant' => $Merchant
        ], 200);
    }
}
