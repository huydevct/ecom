<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    public function addProductToCart($cartId, $productId, $userId)
    {
        $cartFromDB = DB::table('carts')->where('id', $cartId)->where('product_id', $productId)->where('user_id', $userId)->first();
        if ($cartFromDB != null) {
            DB::table('carts')->where('id', $cartId)->where('product_id', $productId)->where('user_id', $userId)->update(array('quantity' => $cartFromDB->quantity + 1));
            return $cartId;
        }

        return DB::table('carts')->insertGetId(
            [
                'quantity' => 1,
                'user_id' => $userId,
                'product_id' => $productId,
            ]
        );
    }

    public function addProductToNewCart($productId, $userId){
        return DB::table('carts')->insertGetId(
            [
                'quantity' => 1,
                'user_id' => $userId,
                'product_id' => $productId,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]
        );
    }

    public function listCartById($cartId)
    {
        $carts = DB::table('carts')->where('id', $cartId)->get();
        if (count($carts) == 0){
            return null;
        }

        $productIds = array();
        foreach ($carts as $cart){
            array_push($productIds, $cart->product_id);
        }

        $products = DB::table('products')->whereIn('id', $productIds)->get();
        foreach ($products as $product){
            foreach ($carts as $cart){
                if ($product->id == $cart->product_id){
                    $product->quantity = $cart->quantity;
                    unset($product->created_at, $product->updated_at);
                }
            }
        }

        $result['id'] = $cartId;
        $result['products'] = $products;

        return $result;
    }
}
