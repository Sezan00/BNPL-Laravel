<?php

namespace App\Http\Controllers;

use App\Models\InstallmentPackeg;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

use function Symfony\Component\Clock\now;

class InstallMentController extends Controller
{
    public function showInstallment()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'unauthorize']);
        }

        $installments  = InstallmentPackeg::where('is_active', 1)->get();

        return response()->json([
            'credit_limit' => $user->credit_limit,
            'installments' => $installments
        ]);
    }

    public function preview(Request $request)
    {
        $request->validate([
        'phone' => 'required|exists:merchants,phone',
        'amount' => 'required|numeric|min:1',
        'package_id' => 'required|exists:installment_packeges,id'
    ]);
     $merchant = Merchant::where('phone', $request->phone)->first();

        $amount = $request->amount;
        $package_id = $request->package_id;

        $package = InstallmentPackeg::find($package_id);

        if (!$package) return response()->json(['error' => 'Package not found'], 404);

        $interest = ($amount * $package->interest_percent / 100) + $package->interest_percent;
        $total_payable = $amount + $interest;

        $installments = [];
        for ($i = 1; $i <= $package->installment_count; $i++) {
            $installments[] = [
                'installment_no' => $i,
                'amount' => round($total_payable / $package->installment_count, 2),
                'due_date' => Carbon::now()->addDays(ceil($package->term_in_days / $package->installment_count * $i))->format('Y-m-d')
            ];
        }

        return response()->json([
            'merchant' => $merchant,
            'amount' => $amount,
            'package_name' => $package->name,
            'interest' => round($interest, 2),
            'total_payable' => round($total_payable, 2),
            'installments' => $installments
        ]);
    }
}
