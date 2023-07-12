<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListOrderByIdRequest;
use App\Models\Order;

class OrderController extends Controller
{
    private $orderModel;
    public function __construct(Order $orderModel){
        $this->orderModel = $orderModel;
    }

    public function listOrderById(ListOrderByIdRequest $request){
        $orderId = $request['order_id'];
        $result = $this->orderModel->listOrderById($orderId);
        return response()->json(['result' => $result], 200);
    }

    public function showOrders(){
        return $this->orderModel->listAllOrder();
    }
}
