<?php

namespace App\Http\Controllers\Ecommerce\ProductDetails;

use App\Services\EcommerceSharedDataService;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\WhatsAppJob;
use Illuminate\Http\Request;
use App\Models\DevicesProduct;
use App\Models\EcommerceProduct;
use App\Http\Controllers\Controller;
use App\Services\SendMessageService;
use App\Models\CustomerProductComment;
use App\Models\CustomerRequestProduct;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ProductDetailsController extends Controller
{
    // في ProductDetailsController
    public function ProductDetails($id)
    {
        $ecommerceSharedData = EcommerceSharedDataService::get();

        $ids = session('recently_viewed', []);
        array_unshift($ids, $id);
        session(['recently_viewed' => array_unique(array_slice($ids, 0, 10))]);

        $Product = EcommerceProduct::with('product')->find($id);

        if (!$Product || !$Product->product) {
            abort(404);
        }

        $productName = $Product->product->product_name;

        $Reviews = CustomerProductComment::where('customerproductcomment_productname', $productName)
            ->where('customerproductcomment_visibility', 1)
            ->latest()
            ->get();

        $reviewsCount = $Reviews->count();
        $avgRating = $Reviews->avg('customerproductcomment_rating') ?? 0;

        $RelatedProducts = EcommerceProduct::where('ecommerceproduct_displaystatus', 1)
            ->where('ecommerceproduct_id', '!=', $id)
            ->with('product')
            ->inRandomOrder()
            ->take(4)
            ->get();

        $SimilarProducts = EcommerceProduct::where('ecommerceproduct_displaystatus', 1)
            ->where('ecommerceproduct_id', '!=', $id)
            ->with('product')
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('ecommerce.ProductDetails.ProductDetails', compact(
            'ecommerceSharedData',
            'Product',
            'Reviews',
            'reviewsCount',
            'avgRating',
            'RelatedProducts',
            'SimilarProducts'
        ));
    }
    public function ProductDetailsPost(Request $request, $id)
    {

        $customerPhone = session('customer_phone');
        // dd($customerPhone);
        // 01020221034

        if ($customerPhone == null) {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' يجب تسجيل الدخول اول حتي نتمكن من استلام طلبك ']);
        }

        $Customer = Customer::where('customer_phone', $customerPhone)->first();

        if (!$Customer) {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' يجب انشاء حساب اول حتي نتمكن من استلام طلبك ']);
        }

        if ($Customer->customer_delete == '1' || $Customer->customer_block == '1') {
            return redirect()
                ->route('home')
                ->withErrors([' تم حظر حسابك , يرجي التواصل مع خدمة العملاء ']);
        }

        // dd($request->all());
        $validator = Validator::make($request->all(), [
            '_token' => 'required|string',
            '_method' => 'required|string|in:post',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $EcommerceProduct = EcommerceProduct::where('product_id', $id)
            ->where('ecommerceproduct_displaystatus', '1')
            ->first();

        if (!$EcommerceProduct) {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' هذا المنتج غير متوافر حاليا ']);
        }

        $Product = Product::where('product_id', $id)
            ->where('product_delete', '0')
            ->first();

        if (!$Product) {
            return redirect()
                ->route('home')
                ->withErrors([' هذا المنتج غير متوافر حاليا ']);
        }

        // 1

        $CustomerRequestProductCount = CustomerRequestProduct::where('customerrequestproduct_customeraccount', $Customer->customer_account)
            ->where('customerrequestproduct_delete', '0')
            ->where('customerrequestproduct_billstatus', '0')
            ->count();


        if ($CustomerRequestProductCount > $Customer->customer_requestlimit) {
            return redirect()
                ->route('EcommerceContactUs')
                ->withErrors([' لقد تجاوزت الحد الاقصي من طلب المنتجات , يرجي التواصل مع خدمة العملاء حتي تم السماح لك بطلبات اخري ']);
        }


        $CustomerRequestProductIds = CustomerRequestProduct::where('customerrequestproduct_customeraccount', $Customer->customer_account)
            ->where('customerrequestproduct_delete', '0')
            ->where('customerrequestproduct_billstatus', '0')
            ->pluck('customerrequestproduct_productname');

        if (in_array($id, $CustomerRequestProductIds->toArray())) {
            return redirect()
                ->route('ShoppingCart')
                ->withErrors([' لقد تم اضافة هذا المنتج من قبل في سلة المشتريات الخاصة بك , يمكن التعديل علي الكمية الخاصة به او حذه من السلة ']);
        }

        // dd($id);

        $Branches = Branche::all();

        $total_product_quantity = 0;

        foreach ($Branches as $Branch) {
            $ProductBranchColumn = $Branch->branch_stock;
            $ProductBranchQuantity = $Product->$ProductBranchColumn;
            if ($ProductBranchQuantity == null) {
                $ProductBranchQuantity = 0;
            }
            $total_product_quantity = $total_product_quantity + $ProductBranchQuantity;
        }

        if ($EcommerceProduct->ecommerceproduct_appearinthelistofoffers == '1') {
            $customerrequestproduct_productpaidprice = $Product->product_offerprice;
        } else {
            $customerrequestproduct_productpaidprice = $Product->product_sellprice;
        }

        $customerrequestproduct_productquantity = '1';
        $customerrequestproduct_producttotalquantityprice = $customerrequestproduct_productquantity * $customerrequestproduct_productpaidprice;

        $now = date('Y-m-d H:i:s');

        $CustomerRequestProduct = new CustomerRequestProduct;
        $CustomerRequestProduct->customerrequestproduct_customeraccount = $Customer->customer_account;
        $CustomerRequestProduct->customerrequestproduct_delete = '0';
        $CustomerRequestProduct->customerrequestproduct_billstatus = '0';
        $CustomerRequestProduct->customerrequestproduct_billreference = null;
        $CustomerRequestProduct->customerrequestproduct_preparedbilldatetime = null;
        $CustomerRequestProduct->customerrequestproduct_productname = $Product->product_id;
        $CustomerRequestProduct->customerrequestproduct_productstockavailability = $total_product_quantity;
        $CustomerRequestProduct->customerrequestproduct_productquantity = $customerrequestproduct_productquantity;
        $CustomerRequestProduct->customerrequestproduct_productbuyprice = $Product->product_buyprice;
        $CustomerRequestProduct->customerrequestproduct_productwholesaleprice = $Product->product_wholesaleprice;
        $CustomerRequestProduct->customerrequestproduct_productofferprice = $Product->product_offerprice;
        $CustomerRequestProduct->customerrequestproduct_productsellprice = $Product->product_sellprice;
        $CustomerRequestProduct->customerrequestproduct_productpaidprice = $customerrequestproduct_productpaidprice;
        $CustomerRequestProduct->customerrequestproduct_producttotalquantityprice = $customerrequestproduct_producttotalquantityprice;
        $CustomerRequestProduct->customerrequestproduct_requestdatetime = $now;
        $CustomerRequestProduct->save();


        $Branch = Branche::where('branch_id', '1')->first();

        $ContantURL = "https://wa.me/+2" . $Customer->customer_phone;

        $Employees = Employee::where('employee_customersorders', '1')->get();

        foreach ($Employees as $Employee) {
            $domain = config('app.url');
            if ($domain == 'https://ricohtecc.plusdigitalpd.com') {
                $message = null;
            } else {

                $message = " يوجد طلب جدبد من عميل  " . $Customer->customer_name . " 🌟\n";
                $message .= " من منتج " . $Product->product_name . " \n";
                $message .= " للتواصل مع العميل " . $ContantURL . " \n";

                $imageUrl = null;
                if ($Employee->employee_phone != null) {
                    $phone = '2' . $Employee->employee_phone;

                    $WhatsAppJob = new WhatsAppJob;
                    $WhatsAppJob->whatsappjob_phone = $phone;
                    $WhatsAppJob->whatsappjob_message = $message;
                    $WhatsAppJob->whatsappjob_image = $imageUrl;
                    $WhatsAppJob->user_name = $Branch->Branch_name;
                    $WhatsAppJob->save();
                }

                if ($Employee->employee_email != null) {
                    $subject = ' طلب منتج جديد من ' . $Customer->customer_name;
                    SendMessageService::sendEmailMessage($Employee->employee_email, $subject, $message, $imageUrl);
                }

                if ($Employee->employee_telegramchatid != null) {
                    SendMessageService::sendTelegramMessage($Employee->employee_telegramchatid, $message, $imageUrl);
                }
            }
        }

        return back()->with('success', ' تم اضافة المنتج في سلة التسوق  بنجاح ');
    }

    public function CustomerAddProductCommentPost(Request $request, $id)
    {

        $customerPhone = session('customer_phone');
        // dd($customerPhone);

        if ($customerPhone == null) {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' يجب تسجيل الدخول اول حتي نتمكن من استلام الرسالة ']);
        }

        // dd($request->all());

        $validator = Validator::make($request->all(), [
            '_token' => 'required|string',
            '_method' => 'required|string|in:post',
            'message' => 'required|string|min:5|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $Customer = Customer::where('customer_phone', $customerPhone)->first();

        $message = $request->message;
        $now = date('Y-m-d H:i:s');

        $CustomerProductComments = CustomerProductComment::where('customerproductcomment_customeraccount', $Customer->customer_account)
            ->where('customerproductcomment_productname', $id)
            ->exists();

        if ($CustomerProductComments) {
            return back()->withErrors(['لقد تم اضافة تعليق لك سابقا علي هذا المنتج']);
        }


        CustomerProductComment::create([
            'customerproductcomment_customeraccount' => $Customer->customer_account,
            'customerproductcomment_productname' => $id,
            'customerproductcomment_comment' => $message,
            'customerproductcomment_visibility' => false,
        ]);


        return back()->with('success', ' تم اضافة تعليقك بنجاح .  سعداء جدا بتواصلك معانا ');
    }
}
