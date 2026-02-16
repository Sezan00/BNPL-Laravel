<?php

namespace App\Http\Controllers;

use App\Models\SettlementAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettlementAccountController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'account_holder_name' => 'required|string',
            'bank_name' => 'required|string',
            'bank_account_number' => 'required|string',
            'bank_adress' => 'nullable|string',
            'bank_branch' => 'nullable|string',
            'ifsc_swift_code' => 'nullable|string',
            'payout_method' => 'required|string',
            'currency' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $merchantId = Auth::id();

        // Check if already active account exists
        $hasActive = SettlementAccount::where('merchant_id', $merchantId)
            ->where('status', 'active')
            ->exists();

        $account = SettlementAccount::create([
            'merchant_id' => $merchantId,
            'account_holder_name' => $request->account_holder_name,
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_adress' => $request->bank_adress,
            'bank_branch' => $request->bank_branch,
            'ifsc_swift_code' => $request->ifsc_swift_code,
            'payout_method' => $request->payout_method,
            'currency' => $request->currency,
            'notes' => $request->notes,
            'status' => $hasActive ? 'inactive' : 'active',
        ]);

        return response()->json([
            'message' => 'Bank account created successfully',
            'data' => $account
        ]);
    }


    public function index()
    {
        //    $merchantId = auth()->id();
        $merchantId = Auth::id();

        $accounts = SettlementAccount::where('merchant_id', $merchantId)
            ->latest()
            ->get();

        return response()->json($accounts);
    }


    public function activate($id)
    {
        //    $merchantId = auth()->id();
        $merchantId = Auth::id();

        $account = SettlementAccount::where('id', $id)
            ->where('merchant_id', $merchantId)
            ->firstOrFail();

        SettlementAccount::where('merchant_id', $merchantId)
            ->update(['status' => 'inactive']);

        $account->update(['status' => 'active']);

        return response()->json([
            'message' => 'Account activated successfully'
        ]);
    }
}
