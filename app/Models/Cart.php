<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    public function addToCart($productId, $cartId){
        $product = DB::table('products')->where('id', $productId)->first();
    }

    public function listCartById($cartId){
        $cart = DB::table('carts')->where('id', $cartId)->first();
        return $cart;
    }
}
