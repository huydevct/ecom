<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    public function getOneProduct($productId)
    {
        $product = DB::table('products')->where('id', $productId)->first();
        return $product;
    }

    public function decreaseOne($productId)
    {
        $productFromDB = DB::table('products')->where('id', $productId)->first();
        if ($productFromDB == null) {
            return "Product not found";
        }
        return DB::table('products')->where('id', $productId)->update(['stock' => $productFromDB->stock - 1]);
    }
}
