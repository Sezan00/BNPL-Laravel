<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
  public function TransactionIndex()
  {
    $user = Auth::user();

    $transaction = Transaction::where('user_id', $user->id)->latest()->get();

    return response()->json([
      'status' => true,
      'data'  => $transaction
    ]);
  }

  public function indexMerchantTransaction()
  {
    $merchant = Auth::user();

    $transactions = Transaction::with('user')
      ->where('merchant_id', $merchant->id)
      ->whereHas('payment', fn($q) => $q->where('receiver_type', 'merchant'))
      ->latest()
      ->get();


    return response()->json([
      'status' => true,
      'data' => $transactions
    ]);
  }
}
