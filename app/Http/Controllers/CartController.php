<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    private $cartModel;
    private $productModel;

    public function __construction(Cart $cartModel, Product $productModel){
        $this->cartModel = $cartModel;
        $this->productModel = $productModel;
    }

    public function addToCart(AddToCartRequest $request){
        $productId = $request->product_id;
        $product = $this->productModel->getOneProduct($$productId);

    }
}
