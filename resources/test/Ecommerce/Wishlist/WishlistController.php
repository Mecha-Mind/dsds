<?php

namespace App\Http\Controllers\Ecommerce\Wishlist;

use App\Http\Controllers\Controller;
use App\Models\EcommerceProduct;
use App\Services\EcommerceSharedDataService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    // ══════════════════════════════════════════════
    // عرض صفحة المفضلة
    // المفضلة في الـ session — تشتغل بدون تسجيل دخول
    // ══════════════════════════════════════════════
    public function index()
    {
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'قائمة الرغبات';

        $wishlistIds = session('wishlist', []);
        $wishlistProducts = collect();

        if (!empty($wishlistIds)) {
            $wishlistProducts = EcommerceProduct::whereIn('ecommerceproduct_id', $wishlistIds)
                ->with('product')
                ->where('ecommerceproduct_displaystatus', 1)
                ->get();
        }

        return view(
            'ecommerce.Wishlist.Wishlist',
            compact('ecommerceSharedData', 'wishlistProducts')
        );
    }

    // ══════════════════════════════════════════════
    // Toggle المفضلة — إضافة أو إزالة
    // بيشتغل بـ AJAX من أي صفحة
    // ══════════════════════════════════════════════
    public function toggle(Request $request, $id)
    {
        $wishlist = session('wishlist', []);
        $inWishlist = in_array($id, $wishlist);

        if ($inWishlist) {
            // إزالة من المفضلة
            $wishlist = array_filter($wishlist, fn($item) => $item != $id);
            $wishlist = array_values($wishlist);
            $action = 'removed';
        } else {
            // إضافة للمفضلة
            $wishlist[] = $id;
            $action = 'added';
        }

        session(['wishlist' => $wishlist]);

        return response()->json([
            'success' => true,
            'action' => $action,
            'in_wishlist' => !$inWishlist,
            'wishlist_count' => count($wishlist),
        ]);
    }
}