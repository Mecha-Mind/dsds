<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends BaseController
{
    public function index()
    {
        $products = Product::paginate(12);
        
        $data = $this->getSharedData();
        $data['products'] = $products;
        
        return view('products.index', $data);
    }

    public function show(Product $product)
    {
        $data = $this->getSharedData();
        $data['product'] = $product;
        
        return view('products.show', $data);
    }
}


    public function account()
    {
        return view('pages.account', $this->sharedData());
    }

    public function orders()
    {
        return view('pages.orders', $this->sharedData());
    }

    public function orderDetail(string $id)
    {
        $data         = $this->sharedData();
        $data['orderId'] = $id;
        return view('pages.order-detail', $data);
    }
}

class ProductController extends Controller
{



    public function index()
    {
        $products = Product::all();
        return view('home', compact('products'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('pages.products', compact('product'));
    }

    public function popular()
    {
        $products = Product::orderBy('views', 'desc')->take(10)->get();
        return view('pages.popular', compact('products'));
    }
    public function recent()
    {
        $products = Product::latest('views', 'desc')->take(5)->get();
        return view('pages.recent', compact('products'));
    }

}
