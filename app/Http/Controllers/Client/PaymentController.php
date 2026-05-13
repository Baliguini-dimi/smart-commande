<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\CinetPayService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private CinetPayService $cinetPay) {}

    // ── Initier le paiement Mobile Money ─────────────
    public function initiate(Request $request, Order $order)
    {
        // Si paiement cash → pas besoin de CinetPay
        if ($order->payment_method === 'cash') {
            return response()->json([
                'success' => true,
                'type'    => 'cash',
                'message' => 'Paiement cash — réglez à la caisse.',
            ]);
        }

        $result = $this->cinetPay->initiatePayment(
            amount:      $order->total_amount,
            orderId:     (string) $order->id,
            description: 'Commande #' . $order->id . ' — Table ' . $order->restaurantTable->number,
            returnUrl:   route('client.payment.return', $order->id),
            notifyUrl:   route('client.payment.notify'),
        );

        if ($result['success']) {
            // Sauvegarde la référence de transaction
            Payment::create([
                'order_id'       => $order->id,
                'amount'         => $order->total_amount,
                'method'         => $order->payment_method,
                'status'         => 'pending',
                'transaction_id' => $result['transaction_id'],
            ]);

            return response()->json([
                'success'     => true,
                'type'        => 'mobile_money',
                'payment_url' => $result['payment_url'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 422);
    }

    // ── Retour après paiement CinetPay ───────────────
    public function return(Order $order)
    {
        // Vérifie le statut du paiement
        $payment = Payment::where('order_id', $order->id)
            ->latest()->first();

        if ($payment) {
            $verification = $this->cinetPay->verifyPayment($payment->transaction_id);

            if ($verification['paid']) {
                $payment->update(['status' => 'success']);
                $order->update(['payment_status' => 'paid']);
            }
        }

        return view('client.payment-return', compact('order', 'payment'));
    }

    // ── Webhook CinetPay (notification serveur) ───────
    public function notify(Request $request)
    {
        $transactionId = $request->cpm_trans_id;

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            return response('Payment not found', 404);
        }

        $verification = $this->cinetPay->verifyPayment($transactionId);

        if ($verification['paid']) {
            $payment->update([
                'status'        => 'success',
                'cinetpay_ref'  => $request->cpm_payid ?? null,
            ]);

            $payment->order->update(['payment_status' => 'paid']);
        } else {
            $payment->update(['status' => 'failed']);
        }

        return response('OK', 200);
    }
}