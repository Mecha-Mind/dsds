<?php

namespace App\Http\Controllers\Ecommerce\ShoppingCart;

use App\Http\Controllers\Controller;
use App\Models\EcommerceProduct;
use App\Services\EcommerceSharedDataService;
use Illuminate\Http\Request;

class ShoppingCartController extends Controller
{
    // ══════════════════════════════════════════════
    // عرض صفحة السلة
    // السلة محفوظة في الـ session — تشتغل بدون تسجيل دخول
    // ══════════════════════════════════════════════
    public function ShoppingCart()
    {
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'سلة المشتريات';

        // المنتجات المشاهدة مؤخراً
        $recentlyViewed = collect();
        $recentIds = session('recently_viewed', []);
        if (!empty($recentIds)) {
            $recentlyViewed = EcommerceProduct::whereIn('ecommerceproduct_id', $recentIds)
                ->with('product')
                ->where('ecommerceproduct_displaystatus', 1)
                ->take(4)
                ->get();
        }

        return view(
            'ecommerce.ShoppingCart.ShoppingCart',
            compact('ecommerceSharedData', 'recentlyViewed')
        );
    }

    // ══════════════════════════════════════════════
    // إضافة منتج للسلة
    // يشتغل بدون تسجيل دخول — Session only
    // ══════════════════════════════════════════════
   // ══════════════════════════════════════════════
    // إضافة للسلة — استقبال الـ quantity
    // ══════════════════════════════════════════════
    public function CustomerRequestIncreseQuantityPost(Request $request, $id)
    {
        $ep = EcommerceProduct::with('product')
            ->where('ecommerceproduct_id', $id)
            ->where('ecommerceproduct_displaystatus', 1)
            ->firstOrFail();

        $cart = session('cart', []);

        // قراءة الكمية من الـ request (من صفحة تفاصيل المنتج)
        $requestedQty = max(1, min((int) ($request->quantity ?? 1), 10));

        if (isset($cart[$id])) {
            // لو موجود — نزود الكمية
            $cart[$id]['quantity'] = min($cart[$id]['quantity'] + $requestedQty, 10);
        } else {
            $cart[$id] = [
                'id'          => $id,
                'name'        => $ep->product?->product_name    ?? '',
                'price'       => (float) ($ep->product?->product_sellprice  ?? 0),
                'offer_price' => (float) ($ep->product?->product_offerprice ?? 0),
                'image'       => $ep->product?->product_image   ?? '',
                'quantity'    => $requestedQty,
            ];
        }

        session(['cart' => $cart]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'cart_count' => count($cart),
                'message'    => 'تمت الإضافة إلى السلة',
            ]);
        }

        return back()->with('success', 'تمت إضافة المنتج إلى السلة');
    }

    // ══════════════════════════════════════════════
    // تعديل الكمية — AJAX response محدث
    // ══════════════════════════════════════════════
    public function CustomerRequestDecreaseQuantityPost(Request $request, $id)
    {
        $cart   = session('cart', []);
        $action = $request->action ?? 'increase';

        if (!isset($cart[$id])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false], 404);
            }
            return back();
        }

        $removed = false;

        if ($action === 'increase') {
            $cart[$id]['quantity'] = min($cart[$id]['quantity'] + 1, 10);

        } elseif ($action === 'decrease') {
            if ($cart[$id]['quantity'] <= 1) {
                // الكمية وصلت لـ 1 — احذف المنتج
                unset($cart[$id]);
                $removed = true;
            } else {
                $cart[$id]['quantity']--;
            }
        }

        session(['cart' => $cart]);

        if ($request->expectsJson()) {
            // حساب إجمالي السلة كلها
            $cartTotal = collect($cart)->sum(function ($item) {
                $price = ($item['offer_price'] ?? 0) > 0 && $item['offer_price'] < $item['price']
                    ? $item['offer_price']
                    : $item['price'];
                return $price * ($item['quantity'] ?? 1);
            });

            // حساب إجمالي المنتج الواحد
            $itemTotal = 0;
            if (!$removed && isset($cart[$id])) {
                $item      = $cart[$id];
                $itemPrice = ($item['offer_price'] ?? 0) > 0 && $item['offer_price'] < $item['price']
                    ? $item['offer_price']
                    : $item['price'];
                $itemTotal = $itemPrice * $item['quantity'];
            }

            return response()->json([
                'success'      => true,
                'removed'      => $removed,                          // الـ JS بيحذف الـ cart-item لو true
                'new_quantity' => $removed ? 0 : $cart[$id]['quantity'],
                'item_total'   => number_format($itemTotal),
                'cart_total'   => number_format($cartTotal),
                'cart_count'   => count($cart),
            ]);
        }

        return back();
    }

    // ══════════════════════════════════════════════
    // حذف المنتج — مش محتاج تعديل
    // بيشتغل بـ form submit عادي من الـ modal
    // ══════════════════════════════════════════════
    public function CustomerRequestDeletePost(Request $request, $id)
    {
        $cart = session('cart', []);
        unset($cart[$id]);
        session(['cart' => $cart]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'cart_count' => count($cart),
            ]);
        }

        return back()->with('success', 'تم حذف المنتج من السلة');
    }
}