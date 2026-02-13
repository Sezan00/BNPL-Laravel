<?php

namespace App\Console\Commands;

use App\Jobs\ChargeInstallmentJob;
use App\Models\InstallmentSchedule;
use Illuminate\Console\Command;
use Carbon\Carbon; 
use function Symfony\Component\Clock\now;

class AutoDebitInstallments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installments:auto-debit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto charge due installments';

    /**
     * Execute the console command.
     */

     
    public function handle()
    {
       $today = Carbon::today()->toDateString();

       $schedules = InstallmentSchedule::where('due_date', '<=', $today)
                   ->where('status', 'pending')->get();

       foreach($schedules as $schedule){
         ChargeInstallmentJob::dispatch($schedule->id);
       }

       $this->info('Auto debit jobs dispatched successfully!');
    }
}
