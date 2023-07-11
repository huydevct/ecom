<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    public function addProductToCart($cartId, $productId, $userId)
    {
        $cartFromDB = DB::table('carts')->where('id', $cartId)->where('product_id', $productId)->where('user_id', $userId)->first();
        if ($cartFromDB != null) {
            $cart = DB::table('carts')->where('id', $cartId)->where('product_id', $productId)->where('user_id', $userId)->update(array('quantity' => $cartFromDB->quantity + 1));
            return $cart;
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
            ]
        );
    }

    public function listCartById($cartId)
    {
        $cart = DB::table('carts')->where('id', $cartId)->first();
        return $cart;
    }
}
