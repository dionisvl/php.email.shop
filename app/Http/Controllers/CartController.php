<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $email = $request->email;
        if (empty($email)) {
            return ['status' => 'error', 'message' => 'email (?email=test@test.ru) required!'];
        } else {
            $cart = $this->indexApi($email);
            if (empty($cart)) {
                $cart['items'] = '{}';
            } else {
                $cart = $cart->toArray();
            }

            $cart['status'] = 'ok';
            $cart['items'] = $this->convertJsonToAMPArray($cart['items']);
            return $cart;
        }
    }

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
     * @param int $itemId
     * @param string $email
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
            $cart->totalPrice = $product['price'];
            $contents[$product['slug']]['count'] = 1;
            $contents[$product['slug']]['data'] = $product;
            $cart->items = json_encode($contents);
            $cart->save();
        } else {
            $contents = $cart->items;
            $contents = json_decode($contents, 1);
            $needProductSlug = $product['slug'];
            if (empty($contents[$needProductSlug])) {
                $contents[$product['slug']]['count'] = 1;
                $contents[$product['slug']]['data'] = $product;
                $cart->totalPrice += $product['price'];
            } else {
                $totalPrice = 0;

                foreach ($contents as $key => $product) {
                    if ($needProductSlug == $product['data']['slug']) {
                        $contents[$key]['count']++;
                    }
                    $totalPrice += $contents[$key]['count'] * $product['data']['price'];
                }
                $cart->totalPrice = $totalPrice;
            }

            $cart->items = json_encode($contents);

            $cart->save();
        }

        $cart = $this->indexApi($email)->toArray();
        $cart['status'] = 'ok';
        $cart['items'] = $this->convertJsonToAMPArray($cart['items']);
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
            $contents = $cart->items;
            $contents = json_decode($contents, 1);

            if (array_key_exists($product['slug'], $contents)) {
                $cart->totalPrice = $cart->totalPrice - $contents[$product['slug']]['count'] * $product['price'];

                unset($contents[$product['slug']]);
                $cart->items = json_encode($contents);
                if (empty($contents)) {
                    $cart->totalPrice = 0;
                }
                $cart->save();
                //return ['status' => 'ok', 'message' => 'success removed'];
            } else {
                //return ['status' => 'ok', 'message' => 'this product does not exists in cart'];
            }
        }

        $cart = $this->indexApi($email)->toArray();
        $cart['status'] = 'ok';
        $cart['items'] = $this->convertJsonToAMPArray($cart['items']);
        return $cart;
    }

    private function convertJsonToAMPArray($json): array
    {
        $arr = json_decode($json, 1);
        $result = [];
        foreach ($arr as $key => $item) {
            $newItem = $item['data'];
            $newItem['count'] = $item['count'];

            $result[] = $newItem;
        }
        return $result;
    }
}
