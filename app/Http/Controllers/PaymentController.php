<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
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

        $receiver     = null;
        $receiverType = null;

        $merchant = Merchant::where('phone', $request->phone)->first();
        if ($merchant) {
            $receiver = $merchant;
            $receiverType = 'merchant';
        } else {
            $user = User::where('phone', $request->phone)->first();
            if (!$user) return response()->json(['Receiver not found'], 401);
            $receiver = $user;
            $receiverType = 'user';
        }

        if ($sender->balance < $request->amount)
            return response()->json(['message' => 'Insufficient balance'], 422);

        try {
            DB::beginTransaction();

        // Payment record
        $payment = Payment::create([
            'sender_id'     => $sender->id,
            'receiver_type' => $receiverType,
            'amount'        => $request->amount,
            'receiver_id'   => $receiver->id,
            'status'        => 'success'
        ]);

        // Sender transaction (debit)
        Transaction::create([
            'user_id' => $sender->id,
            'type' => 'debit',
            'amount' => $request->amount,
            'balance_after' => $sender->balance - $request->amount,
            'payment_id' => $payment->id,
            'description' => "Payment sent to {$receiverType} {$receiver->id}"
        ]);

        // Deduct sender balance
        $sender->decrement('balance', $request->amount);

        // Receiver transaction (credit)
        Transaction::create([
            'user_id' => $receiverType === 'user' ? $receiver->id : null,
            'merchant_id' => $receiverType === 'merchant' ? $receiver->id : null,
            'type' => 'credit',
            'amount' => $request->amount,
            'balance_after' => $receiver->balance + $request->amount,
            'payment_id' => $payment->id,
            'description' => "Payment received from user {$sender->id}"
        ]);

        // Add receiver balance
        $receiver->increment('balance', $request->amount);

        DB::commit();


            return response()->json([
                'message' => 'Payment successful',
                'receiver_type' => $receiverType
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
