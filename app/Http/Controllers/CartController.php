<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddProductNewCartRequest;
use App\Http\Requests\AddToCartRequest;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    private $cartModel;
    private $productModel;

    public function __construct(Cart $cartModel, Product $productModel){
        $this->cartModel = $cartModel;
        $this->productModel = $productModel;
    }

    public function addToExsitedCart(AddToCartRequest $request){
        $productId = $request['product_id'];
        $userId = auth()->user()->id;
        if ($request['cart_id'] == null){
            $product = $this->productModel->getOneProduct($productId);
            if ($product == null){
                return response()->json(['message'=> 'Product not found'], 400);
            }

            $result = $this->cartModel->addProductToNewCart($productId, $userId);
            if ($result == null){
                $this->productModel->decreaseOne($productId);
                return response()->json(['message' => 'Add product to cart failed'], 500);
            }
        }

        $cartId = $request['cart_id'];
        $product = $this->productModel->getOneProduct($productId);
        if ($product == null){
            return response()->json(['message'=> 'Product not found'], 400);
        }

        $result = $this->cartModel->addProductToCart($cartId, $product->id, $userId);
        if ($result){
            $this->productModel->decreaseOne($productId);
            return response()->json(['message' => 'Add product to cart success', 'cart_id' => $result], 201);
        }

        return response()->json(['message' => 'Add product to cart failed'], 500);
    }
}
