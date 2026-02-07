<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\InstallmentPackeg;
use App\Models\InstallmentSchedule;
use App\Models\Merchant;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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


    public function confirmPayment(Request $request){
        $user = Auth::user();
        $amount = $request->amount;
        $package_id = $request->package_id;
        $phone = $request->phone;


        $merchant = Merchant::where('phone', $phone)->first();
        if(!$merchant) return response()->json(['error'=>'Merchant not found'], 404);

        if($user->credit_limit <$amount){
            return response()->json(['error' => 'Insufficient credit limit'], 403);
        }

        DB::beginTransaction();
        try{

            $payment = Payment::create([
                'sender_id' => $user->id,
                'receiver_id' => $merchant->id,
                'receiver_type' => 'merchant',
                'payment_type' => 'pay_later',
                'amount'        => $amount,
                'status'        => 'success'
            ]);

        $package = InstallmentPackeg::find($package_id);
        $interest = ($amount * $package->interest_percent / 100) + $package->fixed_profit;
        $totalPayable  = $amount + $interest;

        $installment = Installment::create([
            'user_id' => $user->id,
            'merchant_id' => $merchant->id,
            'payment_id'  => $payment->id,
            'package_id'  => $package_id,
            'principal_amount' => $amount,
            'interest_amount'  => $interest,
            'total_payable'    => $totalPayable,
            'remaining_balance' => $totalPayable,
            'status' => 'active',
        ]);

        for($i = 1; $i<=$package->installment_count; $i++){
            InstallmentSchedule::create([
                'installment_id' => $installment->id,
                'installment_no' => $i,
                'amount' => round($totalPayable / $package->installment_count, 2),
                'due_date' => Carbon::now()->addDays(ceil($package->term_in_days / $package->installment_count * $i)),
                'status' => 'pending'
            ]);
        }

        $user->credit_limit -= $amount;
        $user->used_credit += $amount;
        $user->save();

        DB::commit();
           return response()->json([
            'success' => true,
            'message' => 'Payment completed'
        ], 200);

        } catch (\Exception $e){
            DB::rollback();
            return response()->json(['error'=>$e->getMessage()], 500);
        }
    }
}
