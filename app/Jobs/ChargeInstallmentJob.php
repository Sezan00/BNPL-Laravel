<?php

namespace App\Jobs;

use App\Models\InstallmentSchedule;
use App\Models\Ledger;
use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class ChargeInstallmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $scheduleId;

    public function __construct($scheduleId)
    {
        $this->scheduleId = $scheduleId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $schedule = InstallmentSchedule::with('installment.user')
                ->find($this->scheduleId);

            if (!$schedule || $schedule->status !== 'pending') {
                return;
            }

            $user = $schedule->installment->user;
            $amount = $schedule->amount;


            $user->charge(
                $amount * 100,
             $payMethod = $user->defaultPaymentMethod()->id
            );

            if(!$payMethod){
                 $schedule->update(['status' => 'failed']);
                 return;   
            }

        

         $payment = Payment::create([
                'sender_id' => $user->id,
                'receiver_type' => 'platform',
                'receiver_id' => null,
                'installment_schedule_id' => $schedule->id,
                'amount' => $amount,
                'status' => 'success',
                'payment_type' => 'pay_later'
            ]);

            $schedule->update([
                'status' => 'paid',
                'paid_at' => now(),
             ]);
        
             $installment = $schedule->installment;
             $installment->paid_amount += $amount;
             $installment->remaining_balance -= $amount;

             if($installment->remaining_balance <=0){
                $installment->status = 'closed';
             }

             $installment->save();


           Ledger::create([
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $user->credit_limit,
                'description' => 'EMI payment'
            ]);


        });
    }
}
