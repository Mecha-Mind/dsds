<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\PageController;


// ── الصفحة الرئيسية ──
Route::get('/', [ShopController::class, 'home'])->name('home');

Route::get('/categories',              [ShopController::class, 'categories'])->name('categories');
Route::get('/category/{category}', [ShopController::class, 'category'])->name('category.show');
Route::get('/subcategory/{subcategory}', [ShopController::class, 'subcategory'])->name('subcategory.show');

Route::get('/maintenance',         [ShopController::class, 'maintenance'])->name('maintenance');
Route::get('/maintenance/offers',  [ShopController::class, 'maintenanceOffers'])->name('maintenance.offers');
Route::get('/allproducts',              [ShopController::class, 'allproducts'])->name('allproducts');
Route::get('/products',              [ShopController::class, 'products'])->name('products');
Route::get('/products/{slug}',       [ShopController::class, 'product'])->name('product.show');

// ── الـ Cart والـ Checkout ──
Route::get('/cart',                  [ShopController::class, 'ShoppingCart'])->name('cart');
Route::get('/checkout',              [ShopController::class, 'checkout'])->name('checkout');
Route::get('/offers',              [ShopController::class, 'offers'])->name('offers');

// ── الحساب ──
Route::get('/account',               [ShopController::class, 'account'])->name('account');
Route::get('/account/orders',        [ShopController::class, 'orders'])->name('account.orders');
Route::get('/account/orders/{id}',   [ShopController::class, 'orderDetail'])->name('account.orders.show');

// ── صفحات ثابتة ──
Route::get('about',   [PageController::class, 'show'])->defaults('slug', 'about')->name('about');
Route::get('contact', [PageController::class, 'contact'])->name('contact');
Route::post('contact', [PageController::class, 'storeContact'])->name('contact.send');
Route::get('faq',     [PageController::class, 'show'])->defaults('slug', 'faq')->name('faq');
Route::get('privacy', [PageController::class, 'show'])->defaults('slug', 'privacy')->name('privacy');
Route::get('terms',   [PageController::class, 'show'])->defaults('slug', 'terms')->name('terms');
Route::get('page/{slug}', [PageController::class, 'show'])->name('page.show');


// Route::get('/', [ProductController::class, 'index'])->name('home');
// Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');