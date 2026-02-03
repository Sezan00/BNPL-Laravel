<?php

namespace App\Http\Controllers;

use App\Models\InstallmentPackeg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstallMentController extends Controller
{
    public function showInstallment(){
        $user = Auth::user();
        if(!$user){
            return response()->json(['message' => 'unauthorize']);
        }

        $installments  = InstallmentPackeg::where('is_active', 1)->get();

        return response()->json([
            'credit_limit' => $user->credit_limit,
            'installments' => $installments 
        ]);

    }
}
