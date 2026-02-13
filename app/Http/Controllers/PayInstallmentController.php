<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\InstallmentSchedule;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayInstallmentController extends Controller
{

   //pay single installment
    public function SingleInstallment(Request $request)
    {
        $user = Auth::user();

        $schedule = InstallmentSchedule::where('id', $request->installment_schedule_id)
            ->where('status', 'pending')
            ->firstOrFail();



        $installment = Installment::findOrFail($schedule->installment_id);

        if ($installment->user_id !== $user->id) {
            return response()->json(['error' => 'unauthorize'], 403);
        }


        DB::beginTransaction();
        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $schedule->amount * 100,
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ]);
            Transaction::create([
                'user_id'        => $user->id,
                'merchant_id'    => $installment->merchant_id,
                'type'           => 'debit',
                'amount'         => $schedule->amount,
                'balance_after'  => $user->credit_limit,
                'payment_id'     => $installment->payment_id,
                'description'    => 'Installment #' . $schedule->installment_no . 'paid',
            ]);

            $schedule->update([
                'status' => 'paid',
                'paid_at' => now(),
                'stripe_payment_id' => $paymentIntent->id,
            ]);

            $installment->remaining_balance -= $schedule->amount;

            if ($installment->remaining_balance <= 0) {
                $installment->status = 'completed';
            }

            $installment->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Installment paid successfully',
                'installment_schedule_id' => $schedule->id
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function ShowAllInstamentData($installmentID){
        
        $installment = Installment::findOrFail($installmentID);

        $pendingSchedules = InstallmentSchedule::where('installment_id', $installment->id)
                ->where('status', 'pending')->get();

        return response()->json([
            'total_pending' => $pendingSchedules->count(),
            'total_amount'  => $pendingSchedules->sum('amount'),
            'schedules'     => $pendingSchedules
        ]);
    }

    //pay all installment directly

    public function PayAllInstallment(Request $request){
        $user = Auth::user();

        $pendingSchedules = InstallmentSchedule::where('installment_id', $request->installment_id)
                    ->where('status', 'pending')->get();
        
        if($pendingSchedules->isEmpty()){
               return response()->json(['error' => 'No pending installments found'], 400);
        }

        // Log::debug('Pending schedules fetched', ['schedules' => $pendingSchedules->toArray()]);



       $installment = Installment::findOrFail($request->installment_id);

       if($installment->user_id !== $user->id){
         return response()->json(['error' => 'Unauthorized'], 403);
       }

       $totalAmount = $pendingSchedules->sum('amount');

       DB::beginTransaction();

       try{
             $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $totalAmount * 100,
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ]);
                 Transaction::create([
                    'user_id'       => $user->id,
                    'merchant_id'   => $installment->merchant_id,
                    'type'          => 'debit',
                    'amount'        => $totalAmount,
                    'balance_after' => $user->credit_limit,
                    'payment_id'    => $installment->payment_id,
                    'description'   => "Paid {$pendingSchedules->count()} pending installments",
        ]);

         foreach($pendingSchedules as $schedule){
            $schedule->update([
                'status' => 'paid',
                'paid_at' => now(),
                'stripe_payment_id' => $paymentIntent->id,
            ]);
         }

         $installment->remaining_balance -= $totalAmount;

         if($installment->remaining_balance <= 0){
            $installment->remaining_balance = 0;
            $installment->status = 'completed';
         }
         $installment->save();

         DB::commit();

         return response()->json([
             'success' => true,
              'total_pending' => $pendingSchedules->count(),
              'total_paid_amount' => $totalAmount,
              'payment_intent_id' => $paymentIntent->id
         ]);

       }  catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
