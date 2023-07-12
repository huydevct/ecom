<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    public function getOneProduct($productId)
    {
        $product = DB::table('products')->where('id', $productId)->first();
        return $product;
    }

    public function decrease($productId, $amount)
    {
        $productFromDB = DB::table('products')->where('id', $productId)->first();
        if ($productFromDB == null) {
            return "Product not found";
        }
        if ($productFromDB->stock < $amount){
            return "Out of stock";
        }
        return DB::table('products')->where('id', $productId)->update(['stock' => $productFromDB->stock - $amount, 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);
    }
}
