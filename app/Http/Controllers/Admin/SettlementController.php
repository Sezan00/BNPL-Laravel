<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Settlement;

class SettlementController extends Controller
{
    public function generate()
    {
        $merchantsPayments = Payment::unsettled()
            ->selectRaw('receiver_id, SUM(amount) as gross_amount')
            ->groupBy('receiver_id')
            ->get();

        foreach ($merchantsPayments as $mp) {
            $gross = $mp->gross_amount;
            $fee = $gross * 0.05;
            $settledAmount = $gross - $fee;

            $settlement = Settlement::create([
                'merchant_id' => $mp->receiver_id,
                'gross_amount' => $gross,
                'total_fee' => $fee,
                'settled_amount' => $settledAmount,
                'currency' => 'USD',
                'status' => 'pending',
            ]);

            Payment::where('receiver_id', $mp->receiver_id)
                ->where('settled_status', 0)
                ->update(['settled_status' => 1]);
        }

        return redirect()->back()->with('success', 'Settlements generated successfully.');
    }
}
