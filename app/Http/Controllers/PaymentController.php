<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Paynow\Payments\Paynow;

class PaymentController extends Controller
{
    private $paynow;

    public function __construct()
    {
        $this->paynow = new Paynow(
            env('PAYNOW_INTEGRATION_ID'),
            env('PAYNOW_INTEGRATION_KEY'),
            'https://paynow.co.zw/gateways/paynow/update',
            'https://paynow.co.zw/return?gateway=paynow'
        );
    }

    public function createPayment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'product' => 'required|array',
            'amount' => 'required|numeric',
            'user_email' => 'required|email',
        ]);

        // Create a new payment
        $payment = $this->paynow->createPayment('Payment for User ' . $request->user_id, $request->user_email);
        foreach ($request->product as $item) {
            $payment->add($item['product'], $item['amount']);
        }

        // Send the payment
        $response = $this->paynow->send($payment);

        if ($response->success()) {
            DB::table('sales')->insert([
                'user_id' => $request->user_id,
                'product' => json_encode($request->product),
                'amount' => $request->amount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Redirect the user to Paynow for payment
            return response()->json(['redirectUrl' => $response->redirectUrl()]);
        } else {
            // Handle errors
            return response()->json(['error' => 'Payment could not be created.'], 500);
        }
    }

    public function paymentUpdate(Request $request)
    {
        // This method handles updates from Paynow
        $status = $this->paynow->pollTransaction($request->pollUrl);
        return response()->json(['status' => 'success']);
    }
}
