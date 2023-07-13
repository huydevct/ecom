<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class Order extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    public function listOrderById($orderId)
    {
        $order = DB::table('orders')->where('orders.id', $orderId)->leftJoin('payments', function ($query){
            $query->on('orders.payment_id', '=', 'payments.id');
        })->get();
        $orderItems = DB::table('order_item')->leftJoin('products', 'order_item.product_id', '=', 'products.id')->where('order_id', $orderId)->get();
        return [
            'items' => $orderItems,
            'order' => $order,
        ];
    }

    public function listAllOrder(){
        $orders = DB::table('orders')->leftJoin('payments', 'orders.payment_id', '=', 'payments.id')->get();

        $orderItems = [];
        foreach ($orders as $order){
            $orderItem = DB::table('order_item')->leftJoin('products', 'order_item.product_id', '=', 'products.id')->where('order_id', $order->id)->get();
            $order->products = $orderItem;
        }

        return [
            'order' => $orders,
        ];
    }
}
