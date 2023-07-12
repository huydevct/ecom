<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddProductNewCartRequest;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\ShowCartRequest;
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

    public function addProductToCart(AddToCartRequest $request){
        $productId = $request['product_id'];
        $amount = $request['amount'];
        $userId = optional(auth()->user())->id;
        if($userId == 0){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($request['cart_id'] == null){
            $product = $this->productModel->getOneProduct($productId);
            if ($product == null){
                return response()->json(['message'=> 'Product not found'], 400);
            }

            if ($product->stock < $amount){
                return response()->json(['message'=> 'Product is not enough'], 400);
            }

            $result = $this->cartModel->addProductToNewCart($productId, $userId, $amount);
            if ($result == null){
                return response()->json(['message' => 'Add product to cart failed'], 500);
            }
        }

        $cartId = $request['cart_id'];
        $product = $this->productModel->getOneProduct($productId);
        if ($product == null){
            return response()->json(['message'=> 'Product not found'], 400);
        }

        $result = $this->cartModel->addProductToCart($cartId, $product->id, $userId, $amount);
        if ($result){
            return response()->json(['message' => 'Add product to cart success', 'cart_id' => $result], 201);
        }

        return response()->json(['message' => 'Add product to cart failed'], 500);
    }

    public function showCart(ShowCartRequest $request){
        $cartId = $request['cart_id'];
        $result = $this->cartModel->listCartById($cartId);
        return response()->json(['data' => $result], 200);
    }
}
