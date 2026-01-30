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

        return response()->json([
            'message' => 'Card saved successfully!'
        ]);
    }
}
