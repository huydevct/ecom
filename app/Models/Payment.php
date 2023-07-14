<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class Payment extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    public function pay($cartId, $userId, $payMethod)
    {
        DB::beginTransaction();
        try {
            $timeNow = Carbon::now()->format('Y-m-d H:i:s');
            $carts = DB::table('carts')->where('id', $cartId)->where('user_id', $userId)->get();
            if (count($carts) == 0) {
                return null;
            }

            $amount = 0;
            $productIds = array();
            foreach ($carts as $cart) {
                if ($cart->status == 'SUCCESS') {
                    continue;
                }
                $amount += $cart->quantity;
                array_push($productIds, $cart->product_id);
            }

            if ($amount == 0) {
                return 'Cart was checked out';
            }

            $products = DB::table('products')->whereIn('id', $productIds)->get();
            $totalPrice = 0.00;
            $productsResult = [];

            foreach ($products as $product) {
                foreach ($carts as $cart) {
                    if ($product->id == $cart->product_id) {
                        $price = $product->price * $cart->quantity;
                        $totalPrice += $price;
                        array_push($productsResult, [
                            $product->id => $cart->quantity,
                        ]);
                    }
                }
            }

            $paymentId = DB::table('payments')->insertGetId([
                'payment_date' => $timeNow,
                'payment_method' => $payMethod,
                'amount' => $amount,
                'user_id' => $userId,
                'created_at' => $timeNow,
            ]);

            $orderId = DB::table('orders')->insertGetId([
                'order_date' => $timeNow,
                'total_price' => $totalPrice,
                'user_id' => $userId,
                'payment_id' => $paymentId,
                'created_at' => $timeNow,
            ]);

            foreach ($products as $product) {
                foreach ($carts as $cart) {
                    if ($product->id == $cart->product_id) {
                        $price = $product->price * $cart->quantity;
                        DB::table('order_item')->insertGetId([
                            'quantity' => $cart->quantity,
                            'price' => $price,
                            'product_id' => $product->id,
                            'order_id' => $orderId,
                            'created_at' => $timeNow,
                        ]);
                    }
                }
            }

            DB::commit();

            return [
                'payment_id' => $paymentId,
                'total_price' => $totalPrice,
                'order_id' => $orderId,
                'products' => $productsResult,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function updateStatusPayment($paymentId, $status)
    {
        return DB::table('payments')->where('id', $paymentId)->update(['status' => $status, 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);
    }

    public function listHistoryPayment($userId){
        return DB::table('payments')->where('user_id', $userId)->whereIn('status', ['SUCCESS','FAILED'])->latest('id')->paginate(20);
    }
}
