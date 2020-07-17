<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    public function indexApi()
    {
        $products = Product::orderBy('products.created_at', 'desc')->paginate(5)->toArray();

        $products['totalPrice'] = 0;
        foreach ($products['data'] as $product) {
            $products['totalPrice'] += $product['price'];
        }
        $products['status'] = 'ok';
        return $products;
    }

    public function demo()
    {
        return view('demo');
    }
}
