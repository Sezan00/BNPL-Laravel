<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\MerchantFee;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function sendPayment(Request $request)
    {
        $sender = Auth::user();
        $request->validate([
            'phone'  => 'required',
            'amount' => 'required|numeric|min:1',
        ]);

        $merchant = Merchant::where('phone', $request->phone)->first();
        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }

        if ($sender->balance < $request->amount)
            return response()->json(['message' => 'Insufficient balance'], 422);

        try {
            DB::beginTransaction();

            $amount = $request->amount;
            $feePercentage = 2;
            $feeAmount = ($amount * $feePercentage / 100);
            $netAmount = $amount - $feeAmount;

            // Payment record
            $payment = Payment::create([
                'sender_id'     => $sender->id,
                'receiver_type' => 'merchant',
                'amount'        => $request->amount,
                'receiver_id'   => $merchant->id,
                'status'        => 'success'
            ]);

            // Sender transaction (debit)
            Transaction::create([
                'user_id' => $sender->id,
                'type' => 'debit',
                'amount' => $request->amount,
                'balance_after' => $sender->balance - $request->amount,
                'payment_id' => $payment->id,
                'description' => "Payment sent to {$merchant->id}"
            ]);

            // Deduct sender balance
            $sender->decrement('balance', $request->amount);

            MerchantFee::create([
                'payment_id' => $payment->id,
                'merchant_id' => $merchant->id,
                'gross_amount' => $amount,
                'fee_percentage' => $feePercentage,
                'fee_amount'  => $feeAmount,
                'net_amount' => $netAmount,
            ]);
            

            // Receiver transaction (credit)
            Transaction::create([
                // 'user_id' => $sender->id,
                'merchant_id' => $merchant->id,
                'type' => 'credit',
                'amount' => $netAmount,
                'balance_after' => $merchant->balance + $netAmount,
                'payment_id' => $payment->id,
                'description' => "Payment received from user {$sender->id}"
            ]);

            // Add receiver balance
            $merchant->increment('balance', $netAmount);

            DB::commit();


            return response()->json([
                'message' => 'Payment successful',
                'receiver_type' => 'merchant'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => "Somethig went wrong!",
                "errors" => $th->getMessage()
            ]);
        }
    }
}
