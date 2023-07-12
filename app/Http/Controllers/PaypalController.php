<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentRequest;
use App\Models\Cart;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    private $paymentModel;
    private $productModel;
    private $cartModel;

    public function __construct(Payment $paymentModel, Product $productModel, Cart $cartModel)
    {
        $this->paymentModel = $paymentModel;
        $this->productModel = $productModel;
        $this->cartModel = $cartModel;
    }

    public function handlePayment(CreatePaymentRequest $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        $cartId = $request['cart_id'];
        $userId = optional(auth()->user())->id;
        if ($userId == null) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $result = $this->paymentModel->pay($cartId, $userId, 'Paypal');
        if ($result == null) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        if ($result == 'Cart was checked out'){
            return response()->json(['message' => $result], 400);
        }

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('success.payment'),
                "cancel_url" => route('cancel.payment'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => sprintf("%.2f", $result['total_price'])
                    ]
                ]
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    $res = $this->paymentModel->updateStatusPayment($result['payment_id'], "SUCCESS");
                    if ($res == 0) {
                        return response()->json(['message' => 'error update payment success'], 500);
                    }

                    $this->cartModel->updateStatusCart($cartId, 'SUCCESS');

                    foreach ($result['products'][0] as $key => $value) {
                        $this->productModel->decrease($key, $value);
                    }

                    redirect()->away($links['href']);
                    return response()->json(['result' => $result], 200);
                }
            }

            $res = $this->paymentModel->updateStatusPayment($result['payment_id'], "FAILED");
            if ($res == 0) {
                return response()->json(['message' => 'error update payment failed'], 500);
            }

            $this->cartModel->updateStatusCart($cartId, 'FAILED');

            return redirect()
                ->route('cancel.payment')
                ->with('error', 'Something went wrong.');
        } else {
            $res = $this->paymentModel->updateStatusPayment($result['payment_id'], "FAILED");
            if ($res == 0) {
                return response()->json(['message' => 'error update payment failed'], 500);
            }

            $this->cartModel->updateStatusCart($cartId, 'FAILED');

            return redirect()
                ->route('create.payment')
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }

    public function paymentCancel()
    {
        return redirect()
            ->route('create.payment')
            ->with('error', $response['message'] ?? 'You have canceled the transaction.');
    }

    public function paymentSuccess(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            return redirect()
                ->route('create.payment')
                ->with('success', 'Transaction complete.');
        } else {
            return redirect()
                ->route('create.payment')
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }
}
