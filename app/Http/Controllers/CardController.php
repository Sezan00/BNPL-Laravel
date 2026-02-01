<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        if (!$user->stripe_id) {
            $user->createAsStripeCustomer();
        }

        $intent = $user->createSetupIntent();

        return response()->json([
            'client_secret' => $intent->client_secret,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $paymentMethodID = $request->payment_method;

        $user->addPaymentMethod($paymentMethodID);
        $user->updateDefaultPaymentMethod($paymentMethodID);

        // $extraCredit = 500;
            // if (bccomp($user->credit_limit, '0.00', 2) === 0) {
            //     $update = $user->update([
            //         'credit_limit' => $extraCredit,
            //         'balance' => $user->balance + $extraCredit,
            //     ]);
            // }

            
         $extraCredit = 500;

        if($user->credit_limit == 0){
            $update = $user->update([
               'credit_limit' => $extraCredit,
            ]);
        }

        logger('User update', [
            'updated' => $update
        ]);

        return response()->json([
            'message' => 'Card saved successfully!',
            'credit_limit' => $user->fresh()->credit_limit,
        ]);
    }
}
