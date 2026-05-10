<?php

namespace App\Http\Controllers\Ecommerce\Checkout;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerRequestProduct;
use App\Models\EcommerceProduct;
use App\Models\Product;
use App\Services\EcommerceSharedDataService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
  // ══════════════════════════════════════════════
  // عرض صفحة الـ Checkout
  // ══════════════════════════════════════════════
  public function index()
  {
    $ecommerceSharedData = EcommerceSharedDataService::get();
    $ecommerceSharedData['pageTitle'] = 'إتمام الطلب';

    $cart = session('cart', []);

    // لو السلة فارغة — رجّع للسلة
    if (empty($cart)) {
      return redirect()->route('ShoppingCart')
        ->with('error', 'سلتك فارغة، أضف منتجات أولاً');
    }

    $cartTotal = collect($cart)->sum(function ($item) {
      $price = ($item['offer_price'] ?? 0) > 0 && $item['offer_price'] < $item['price']
        ? $item['offer_price']
        : $item['price'];
      return $price * ($item['quantity'] ?? 1);
    });

    return view(
      'ecommerce.Checkout.Checkout',
      compact('ecommerceSharedData', 'cart', 'cartTotal')
    );
  }

  // ══════════════════════════════════════════════
  // تأكيد الطلب
  // هنا بنحول الـ session cart لـ CustomerRequestProduct في الـ DB
  // ══════════════════════════════════════════════
  public function confirm(Request $request)
  {
    // لازم يكون مسجل دخول
    $customerPhone = session('customer_phone');
    if (!$customerPhone) {
      return redirect()->route('CustomerLogin')
        ->with('error', 'يجب تسجيل الدخول لإتمام الطلب');
    }

    $customer = Customer::where('customer_phone', $customerPhone)->first();
    if (!$customer) {
      return redirect()->route('CustomerLogin');
    }

    $cart = session('cart', []);
    if (empty($cart)) {
      return redirect()->route('ShoppingCart');
    }

    foreach ($cart as $id => $item) {
      /*
       | نتحقق إن المنتج لسه متاح
      */
      $ep = EcommerceProduct::with('product')
        ->where('ecommerceproduct_id', $id)
        ->where('ecommerceproduct_displaystatus', 1)
        ->first();

      if (!$ep || !$ep->product)
        continue;

      $product = $ep->product;

      // نتحقق إنه مش متضاف قبل كده
      $exists = CustomerRequestProduct::where('customerrequestproduct_customeraccount', $customer->customer_account)
        ->where('customerrequestproduct_productname', $product->product_id)
        ->where('customerrequestproduct_delete', '0')
        ->where('customerrequestproduct_billstatus', '0')
        ->exists();

      if ($exists)
        continue;

      $paidPrice = $ep->ecommerceproduct_appearinthelistofoffers == '1'
        ? $product->product_offerprice
        : $product->product_sellprice;

      $quantity = $item['quantity'] ?? 1;

      CustomerRequestProduct::create([
        'customerrequestproduct_customeraccount' => $customer->customer_account,
        'customerrequestproduct_delete' => '0',
        'customerrequestproduct_billstatus' => '0',
        'customerrequestproduct_billreference' => null,
        'customerrequestproduct_preparedbilldatetime' => null,
        'customerrequestproduct_productname' => $product->product_id,
        'customerrequestproduct_productstockavailability' => 0,
        'customerrequestproduct_productquantity' => $quantity,
        'customerrequestproduct_productbuyprice' => $product->product_buyprice,
        'customerrequestproduct_productwholesaleprice' => $product->product_wholesaleprice,
        'customerrequestproduct_productofferprice' => $product->product_offerprice,
        'customerrequestproduct_productsellprice' => $product->product_sellprice,
        'customerrequestproduct_productpaidprice' => $paidPrice,
        'customerrequestproduct_producttotalquantityprice' => $paidPrice * $quantity,
        'customerrequestproduct_requestdatetime' => now()->toDateTimeString(),
      ]);
    }

    // مسح السلة بعد تأكيد الطلب
    session()->forget('cart');

    return redirect()->route('ShoppingCart')
      ->with('success', 'تم تأكيد طلبك بنجاح! سنتواصل معك قريباً.');
  }
}