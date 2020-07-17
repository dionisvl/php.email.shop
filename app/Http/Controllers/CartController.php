<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function indexApi(string $email)
    {
        return Cart::where('email', $email)->first();
    }

    function addOneApi(int $itemId, string $email)
    {
        return $this->addOne($itemId, $email);
    }

    public function addApi(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $email = $content['userEmail'];
        $itemId = $content['product']['id'];

        return $this->addOne($itemId, $email);
    }

    /**
     * add one item to cart
     * and return actual cart
     * @param Request $request
     * @return mixed
     */
    private function addOne(int $itemId, string $email)
    {
        $product = Product::where('id', $itemId)->first()->toArray();

        $cart = $this->indexApi($email);
        if (empty($cart)) {
            $cart = new Cart();
            $cart->title = $email;
            $cart->slug = $email;
            $cart->email = $email;
            $cart->total_price = $product['price'];
            $contents[$product['slug']]['count'] = 1;
            $contents[$product['slug']]['data'] = $product;
            $cart->contents_json = json_encode($contents);
            $cart->save();
        } else {

            $contents = $cart->contents_json;
            $contents = json_decode($contents, 1);

            if (array_key_exists($product['slug'], $contents)) {
                $totalPrice = 0;

                foreach ($contents as $key => $product) {

                    if ($key == $product['data']['slug']) {
                        $contents[$key]['count']++;
                    }
                    $totalPrice += $contents[$key]['count'] * $product['data']['price'];
                }
                $cart->total_price = $totalPrice;
            } else {
                $contents[$product['slug']]['count'] = 1;
                $contents[$product['slug']]['data'] = $product;
                $cart->total_price += $product['price'];
            }

            $cart->contents_json = json_encode($contents);

            $cart->save();
        }

        $cart = $this->indexApi($email)->toArray();
        $cart['status'] = 'ok';
        return $cart;
    }

    public function delApi(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $email = $content['userEmail'];
        $itemId = $content['product']['id'];
        return $this->delOne($itemId, $email);
    }

    public function delOneApi(int $itemId, string $email)
    {
        return $this->delOne($itemId, $email);
    }

    private function delOne(int $itemId, string $email)
    {
        $product = Product::where('id', $itemId)->first()->toArray();

        $cart = $this->indexApi($email);
        if (empty($cart)) {
            return ['status' => 'ok', 'message' => 'cart already empty'];
        } else {
            $contents = $cart->contents_json;
            $contents = json_decode($contents, 1);

            if (array_key_exists($product['slug'], $contents)) {
                $cart->total_price = $cart->total_price - $contents[$product['slug']]['count'] * $product['price'];

                unset($contents[$product['slug']]);
                $cart->contents_json = json_encode($contents);
                $cart->save();
                //return ['status' => 'ok', 'message' => 'success removed'];
            } else {
                //return ['status' => 'ok', 'message' => 'this product does not exists in cart'];
            }
        }

        $cart = $this->indexApi($email)->toArray();
        $cart['status'] = 'ok';
        return $cart;
    }
}
