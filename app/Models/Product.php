<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    public function getOneProduct($productId){
        $product = DB::table('products')->where('id', $productId)->first();
        return $product;
    }
}
