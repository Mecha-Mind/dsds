<!-- handler -->
 <?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use App\Services\EcommerceSharedDataService;
use Illuminate\Support\Facades\View;
class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // $this->reportable(function (Throwable $e) {
        //     //
        // });
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            try {
                $ecommerceSharedData = EcommerceSharedDataService::get();
            } catch (\Exception $ex) {
                $ecommerceSharedData = [
                    'staticLinks' => [],
                    'navData' => [],
                    'branchName' => '',
                    'branchImage' => '',
                    'phone' => '',
                    'logo' => '',
                    'branch' => null,
                    'social' => null,
                    'mapUrl' => '#',
                    'footerCategories' => collect(),
                ];
            }

            return response()->view('errors.404', compact('ecommerceSharedData'), 404);
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ThrottleRequestsException) {
            return redirect()->back()
                ->withErrors(['error' => ' لقد قمت بالضغط كثير لتنفيذ الامر يرجي الانتظار حتي الانتهاء من الامور السابقة ']);
        }

        return parent::render($request, $exception);
    }

}

<!-- check-ctr -->
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
<!-- cust-co -->
 <?php

namespace App\Http\Controllers\Ecommerce\Customer\CustomerCode;

use App\Models\Account;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\ScrollingOffer;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceCompany;
use App\Models\Offersfromtheowner;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Models\LoginHistoriesCustomer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CustomerCodeController extends Controller
{

    public function CustomerCodePost(Request $request)
    {
        try {
            // Log::info('Start CustomerCodePost', ['request' => $request->all()]);

            // ✅ 1) فك التشفير
            $decryptedcustomer_name = Crypt::decryptString($request->input('customer_name'));
            $decryptedcustomer_phone = Crypt::decryptString($request->input('customer_phone'));
            $decryptedcustomer_systemcode = Crypt::decryptString($request->input('customer_systemcode'));
            $decryptedcustomer_telegramchatid = $request->filled('customer_telegramchatid')
                ? Crypt::decryptString($request->input('customer_telegramchatid'))
                : null;
            $decryptedcustomer_email = $request->filled('customer_email')
                ? Crypt::decryptString($request->input('customer_email'))
                : null;

            $request->merge([
                'customer_name' => $decryptedcustomer_name,
                'customer_phone' => $decryptedcustomer_phone,
                'customer_systemcode' => $decryptedcustomer_systemcode,
                'customer_telegramchatid' => $decryptedcustomer_telegramchatid,
                'customer_email' => $decryptedcustomer_email,
            ]);

            // Log::info('After decrypting request', ['request' => $request->all()]);

            // ✅ 2) التحقق من الكود
            if ($request->customer_code != $request->customer_systemcode) {
                // Log::warning('Customer code mismatch', [
                //     'customer_code' => $request->customer_code,
                //     'customer_systemcode' => $request->customer_systemcode
                // ]);
                return redirect()->route('CustomerLogin')
                    ->withErrors(['error' => 'الكود الذي تم إدخاله غير صحيح']);
            }
            // Log::info('Customer code verified');

            // ✅ 3) التحقق من وجود العميل
            $Customer = Customer::where('customer_phone', $request->customer_phone)->first();
            // Log::info('Customer fetched', ['Customer' => $Customer]);

            // ✅ 4) Validation عامة حسب إذا كان عميل جديد أو قديم
            $rules = [
                '_token' => 'required|string',
                '_method' => 'required|string|in:post',
                'customer_name' => 'required|string|regex:/^[\p{L}\s]+$/u|max:255',
                'customer_systemcode' => 'required|numeric|digits:6',
            ];

            if (!$Customer) {
                $rules['customer_phone'] = 'required|string|regex:/^01[0-9]{9}$/|max:11|unique:customers,customer_phone';
                $rules['customer_email'] = 'nullable|email|max:255';
                $rules['customer_telegramchatid'] = 'nullable|string|max:255|unique:customers,customer_telegramchatid';
            } else {
                $rules['customer_phone'] = [
                    'required',
                    'string',
                    'regex:/^01[0-9]{9}$/',
                    'max:11',
                    Rule::unique('customers', 'customer_phone')->ignore($Customer->customer_id, 'customer_id')
                ];
                $rules['customer_email'] = 'nullable|email|max:255';
                $rules['customer_telegramchatid'] = [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('customers', 'customer_telegramchatid')->ignore($Customer->customer_id, 'customer_id')
                ];
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // Log::warning('Validation failed', ['errors' => $validator->errors()]);
                return back()->withErrors($validator)->withInput();
            }
            // Log::info('Validation passed');

            // 1️⃣ جلب الـ IP والموقع
            $ip = $_SERVER['REMOTE_ADDR'];
            $public_ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
                ? $ip
                : @file_get_contents('https://api.ipify.org');

            $url = "http://ip-api.com/json/{$public_ip}";
            $response = @file_get_contents($url);
            $data = json_decode($response, true);

            $loginhistory_city = $loginhistory_country = $loginhistory_latitude = $loginhistory_longitude = $loginhistory_location = 'not found';
            if ($data && $data['status'] == 'success') {
                $loginhistory_city = htmlspecialchars($data['city']);
                $loginhistory_country = htmlspecialchars($data['country']);
                $loginhistory_latitude = htmlspecialchars($data['lat']);
                $loginhistory_longitude = htmlspecialchars($data['lon']);
                $loginhistory_location = "https://www.google.com/maps?q={$loginhistory_latitude},{$loginhistory_longitude}";
            }
            // Log::info('IP & Location fetched', [
            //     'public_ip' => $public_ip,
            //     'city' => $loginhistory_city,
            //     'country' => $loginhistory_country,
            //     'location' => $loginhistory_location
            // ]);

            if ($loginhistory_location == 'not found' && $public_ip != '127.0.0.1') {
                // Log::warning('Location not found and not localhost');
                return back()->withErrors([
                    'user_name' => 'لا يمكن تحديد موقع دخولك للنظام لذلك يجب تغير المكان الذي تحول الدخل منه',
                ])->withInput();
            }

            // ✅ Transaction
            DB::transaction(function () use ($request, $Customer, $public_ip, $loginhistory_city, $loginhistory_country, $loginhistory_latitude, $loginhistory_longitude, $loginhistory_location) {
                // Log::info('Start DB transaction');

                $user_name = 'Customer_' . $request->customer_phone;

                if (!$Customer) {
                    // Log::info('Creating new customer account');

                    $account = Account::updateOrCreate(
                        ['account_type' => 'person', 'account_phone' => $request->customer_phone],
                        [
                            'account_name' => $request->customer_name,
                            'account_level_1' => 'الاصول',
                            'account_level_2' => 'الاصول المتداولة',
                            'account_level_3' => 'العملاء',
                            'account_level_4' => $request->customer_name,
                            'user_name' => $user_name,
                        ]
                    );

                    $Customer = Customer::create([
                        'customer_name' => $request->customer_name,
                        'customer_email' => $request->customer_email,
                        'customer_phone' => $request->customer_phone,
                        'customer_account' => $account->account_id,
                        'customer_telegramchatid' => $request->customer_telegramchatid,
                        'user_name' => $user_name,
                    ]);

                    // Log::info('New customer created', ['Customer' => $Customer]);
                } else {
                    // Log::info('Updating existing customer', ['Customer' => $Customer]);

                    if ($Customer->customer_block == '1' || $Customer->customer_delete == '1') {
                        // Log::error('Customer blocked or deleted');
                        throw new \Exception('تم حظر حسابك من الدخول للموقع. يرجي التواصل مع خدمة العملاء.');
                    }

                    $CustomerAccount = Account::find($Customer->customer_account);
                    if (!$CustomerAccount) {
                        // Log::error('Customer account not found');
                        throw new \Exception('يوجد خطأ في حسابك. يرجي التواصل مع خدمة العملاء.');
                    }

                    $CustomerAccount->update([
                        'account_type' => 'person',
                        'account_phone' => $request->customer_phone,
                        'account_name' => $request->customer_name,
                        'account_level_1' => 'الاصول',
                        'account_level_2' => 'الاصول المتداولة',
                        'account_level_3' => 'العملاء',
                        'account_level_4' => $request->customer_name,
                        'user_name' => $user_name,
                    ]);

                    $Customer->update([
                        'customer_name' => $request->customer_name,
                        'customer_email' => $request->customer_email,
                        'customer_phone' => $request->customer_phone,
                        'customer_telegramchatid' => $request->customer_telegramchatid,
                    ]);
                    // Log::info('Existing customer updated');
                }

                // تسجيل الدخول
                LoginHistoriesCustomer::where('loginhistorycustomer_phone', $request->customer_phone)->delete();
                LoginHistoriesCustomer::create([
                    'loginhistorycustomer_name' => $request->customer_name,
                    'loginhistorycustomer_telegramchatid' => $request->customer_telegramchatid,
                    'loginhistorycustomer_email' => $request->customer_email,
                    'loginhistorycustomer_phone' => $request->customer_phone,
                    'loginhistorycustomer_systemcode' => $request->customer_systemcode,
                    'loginhistorycustomer_code' => $request->customer_code,
                    'loginhistorycustomer_account' => $Customer->customer_account,
                    'loginhistorycustomer_amount' => $Customer->customer_amount ?? '0.00',
                    'loginhistorycustomer_ip' => $public_ip,
                    'loginhistorycustomer_city' => $loginhistory_city,
                    'loginhistorycustomer_country' => $loginhistory_country,
                    'loginhistorycustomer_latitude' => $loginhistory_latitude,
                    'loginhistorycustomer_longitude' => $loginhistory_longitude,
                    'loginhistorycustomer_location' => $loginhistory_location,
                    'loginhistorycustomer_entertime' => now('Africa/Cairo'),
                ]);
                // Log::info('Login history created');
            });

            // ✅ إدارة الـ Session
            $request->session()->regenerate();
            $request->session()->put('customer_phone', $request->customer_phone);
            $request->session()->put('customer_name', $request->customer_name);

            // ✅ جلب بيانات الواجهة
            if (session('customer_phone')) {

                $ScrollingOffers = ScrollingOffer::where('scrollingoffer_active', '1')
                    ->orderBy('updated_at', 'desc')
                    ->get();

                $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
                    ->whereNotNull('maintenancecompany_image')
                    ->orderBy('updated_at', 'desc')
                    ->get();

                $ThemostsellingEcommerceproducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
                    ->where('ecommerceproduct_appearinbestsellers', '1')
                    ->where('ecommerceproduct_appearonhomepage', '1')
                    ->orderBy('updated_at', 'desc')
                    ->take(8)
                    ->get();

                $Products = Product::all();

                $Categories = Category::where('category_displaystatus', '1')
                    ->where('category_appearonhomepage', '1')
                    ->orderBy('updated_at', 'desc')
                    ->take(6)
                    ->get();

                $Productswithoffersanddiscounts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
                    ->where('ecommerceproduct_appearinthelistofoffers', '1')
                    ->where('ecommerceproduct_appearonhomepage', '1')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(4);

                $Offersfromtheowners = Offersfromtheowner::where('offerfromtheowner_active', '1')
                    ->whereNotNull('offerfromtheowner_image')
                    ->orderBy('updated_at', 'desc')
                    ->take(3)
                    ->get();

                $ecommerceSharedData = EcommerceSharedDataService::get();
                $ecommerceSharedData['pageTitle'] = 'مستخدم جديد';

                return view('ecommerce.Customer.CustomerWelcome.CustomerWelcome', compact(
                    'ecommerceSharedData',
                    'Offersfromtheowners',
                    'Productswithoffersanddiscounts',
                    'Categories',
                    'ScrollingOffers',
                    'PartnerCompanies',
                    'ThemostsellingEcommerceproducts',
                    'Products'
                ));
            }
            return redirect()->route('CustomerLogin')->withErrors([
                'يوجد خطأ في البيانات التي تم إدخالها. يرجي التواصل مع خدمة العملاء.'
            ]);
        } catch (\Exception $e) {
            // Log::error('CustomerCodePost Exception', ['message' => $e->getMessage()]);
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
<!-- cust-log -->
 <?php

namespace App\Http\Controllers\Ecommerce\Customer\CustomerLogin;

use Carbon\Carbon;
use App\Models\Account;
use App\Services\EcommerceSharedDataService;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\WhatsAppJob;
use Illuminate\Http\Request;
use App\Models\ScrollingOffer;
use App\Models\CustomerAttempt;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceCompany;
use App\Models\Offersfromtheowner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\SendMessageService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\LoginHistoriesCustomer;
use Illuminate\Support\Facades\Validator;

class CustomerLoginController extends Controller
{
    public function CustomerLogin()
    {

        $ScrollingOffers = ScrollingOffer::where('scrollingoffer_active', '1')
            ->orderBy('updated_at', 'desc')
            ->get();

        $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->orderBy('updated_at', 'desc')
            ->get();

        $ThemostsellingEcommerceproducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->where('ecommerceproduct_appearinbestsellers', '1')
            ->where('ecommerceproduct_appearonhomepage', '1')
            ->orderBy('updated_at', 'desc')
            ->take(8)
            ->get();

        $Products = Product::all();

        $Categories = Category::where('category_displaystatus', '1')
            ->where('category_appearonhomepage', '1')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        $Productswithoffersanddiscounts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->where('ecommerceproduct_appearinthelistofoffers', '1')
            ->where('ecommerceproduct_appearonhomepage', '1')
            ->orderBy('updated_at', 'desc')
            ->paginate(4);

        $Offersfromtheowners = Offersfromtheowner::where('offerfromtheowner_active', '1')
            ->whereNotNull('offerfromtheowner_image')
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'مستخدم جديد';

        return view('ecommerce.Customer.CustomerLogin.CustomerLogin', compact(
            'ecommerceSharedData',
            'Offersfromtheowners',
            'Productswithoffersanddiscounts',
            'Categories',
            'ScrollingOffers',
            'PartnerCompanies',
            'ThemostsellingEcommerceproducts',
            'Products'
        ));
    }


    public function CustomerLoginPost(Request $request)
    {
        // ✅ 1) Validation الأول
        $validator = Validator::make($request->all(), [
            '_token' => 'required|string',
            '_method' => 'required|string|in:post',
            'customer_phone' => 'required|string|regex:/^01[0-9]{9}$/',
            'customer_password' => 'nullable',
            'string',
            'min:6',
            'max:255',
            'fingerprint' => 'nullable|string',
            'client_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {

            return back()->withErrors($validator)->withInput();
        }

        // ✅ 2) بناء device_hash آمن
        $fingerprint = $request->input('fingerprint', '');
        $clientToken = $request->input('client_token', '');
        $ua = $request->header('User-Agent', 'unknown');

        $deviceData = $fingerprint . '|' . $clientToken . '|' . $ua;
        $deviceHash = hash_hmac('sha256', $deviceData, config('app.key'));

        $phone = $request->input('customer_phone');
        $ip = $request->ip();
        $now = Carbon::now();

        // إعدادات السياسة
        $MAX_ATTEMPTS = 5;
        $WINDOW_MINUTES = 60;
        $BLOCK_DURATION_MINUTES = 60;

        // ✅ 3) إدارة المحاولات
        try {
            $result = DB::transaction(function () use ($phone, $deviceHash, $ip, $MAX_ATTEMPTS, $WINDOW_MINUTES, $BLOCK_DURATION_MINUTES, $now) {


                $attempt = CustomerAttempt::where('customer_phone', $phone)
                    ->orWhere('device_hash', $deviceHash)
                    ->first();

                if (!$attempt) {

                    $attempt = CustomerAttempt::create([
                        'customer_phone' => $phone,
                        'device_hash' => $deviceHash,
                        'ip_address' => $ip,
                        'attempts' => 1,
                        'last_attempt_at' => $now,
                        'blocked_until' => null,
                    ]);
                    return ['status' => 'ok']; // أول محاولة عادي
                }

                // لو محظور
                if ($attempt->blocked_until && $now->lt(Carbon::parse($attempt->blocked_until))) {

                    return ['status' => 'blocked', 'blocked_until' => Carbon::parse($attempt->blocked_until)];
                }

                // Reset attempts لو النافذة خلصت
                $windowStart = $now->copy()->subMinutes($WINDOW_MINUTES);
                if (!$attempt->last_attempt_at || Carbon::parse($attempt->last_attempt_at)->lt($windowStart)) {

                    $attempt->attempts = 0;
                }

                // زود العداد
                $attempt->attempts++;
                $attempt->last_attempt_at = $now;
                $attempt->ip_address = $ip;

                // حظر لو وصل الحد
                if ($attempt->attempts >= $MAX_ATTEMPTS) {
                    $attempt->blocked_until = $now->copy()->addMinutes($BLOCK_DURATION_MINUTES);
                    $attempt->save();

                    return ['status' => 'blocked_now', 'blocked_until' => $attempt->blocked_until];
                }

                $attempt->save();

                return ['status' => 'ok'];
            }, 3);
        } catch (\Throwable $e) {

            return back()->withErrors(['error' => 'حصل خطأ في التحقق. حاول تاني بعد شوية.'])->withInput();
        }

        // ✅ 4) التعامل مع حالة الحظر
        if ($result['status'] !== 'ok') {
            if ($result['status'] === 'blocked' || $result['status'] === 'blocked_now') {
                return back()->withErrors(['error' => 'لقد تجاوزت عدد المحاولات المسموح بها. حاول بعد '
                    . $result['blocked_until']->diffForHumans() . '.'])->withInput();
            }
            return back()->withErrors(['error' => 'حصل خطأ في التحقق. حاول لاحقًا.'])->withInput();
        }

        $customer_phone = $request->customer_phone;

        $Customer = Customer::where('customer_phone', $customer_phone)->first();

        // ✅ 2) بيانات الواجهة المشتركة (عشان منكررش الكويريز)
        $commonData = $this->getCommonData($customer_phone);

        // ✅ 3) لو العميل مش موجود → عرض صفحة التسجيل
        if (!$Customer || $Customer->customer_phone == null) {
            $encryptedcustomer_phone = Crypt::encryptString($customer_phone);
            return view('ecommerce.Customer.NewCustomer.NewCustomer', array_merge($commonData, [
                'encryptedcustomer_phone' => $encryptedcustomer_phone
            ]));
        }

        if ($request->customer_password && $request->customer_password != null) {
            $plainPassword = $request->customer_password;
            // التحقق من تطابق كلمة المرور
            try {
                // ✅ 1) جمع الفاليديشن أولًا
                $validator = Validator::make($request->all(), [
                    'customer_phone' => [
                        'required',
                        'string',
                        'regex:/^01[0-9]{9}$/',
                        'max:11'
                    ],
                    'customer_password' => 'required|string|min:6|max:255',
                ], [
                    'customer_phone.required' => 'رقم الهاتف مطلوب',
                    'customer_password.required' => 'كلمة المرور مطلوبة',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $customer_phone = $request->customer_phone;
                $plainPassword  = $request->customer_password;

                // ✅ 2) جلب العميل والتأكد من وجوده وصحته
                $Customer = Customer::where([
                    ['customer_phone', '=', $customer_phone],
                    ['customer_block', '=', 0],
                    ['customer_delete', '=', 0],
                ])->first();

                if (!$Customer) {
                    return back()->withErrors(['customer_phone' => '⚠️ هذا الحساب غير موجود أو محظور'])->withInput();
                }

                // ✅ 3) التحقق من كلمة المرور
                if (!Hash::check($plainPassword, $Customer->customer_password)) {
                    return back()->withErrors(['customer_password' => '⚠️ كلمة المرور غير صحيحة'])->withInput();
                }

                // ✅ 4) تحديد الموقع والـ IP (تحقق خفيف)
                $ip = $_SERVER['REMOTE_ADDR'] ?? request()->ip();
                $public_ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
                    ? $ip
                    : @file_get_contents('https://api.ipify.org');

                $loginhistory_city = $loginhistory_country = $loginhistory_latitude = $loginhistory_longitude = $loginhistory_location = 'not found';

                $url = "http://ip-api.com/json/{$public_ip}";
                $response = @file_get_contents($url);
                $data = json_decode($response, true);

                if ($data && $data['status'] === 'success') {
                    $loginhistory_city = htmlspecialchars($data['city']);
                    $loginhistory_country = htmlspecialchars($data['country']);
                    $loginhistory_latitude = htmlspecialchars($data['lat']);
                    $loginhistory_longitude = htmlspecialchars($data['lon']);
                    $loginhistory_location = "https://www.google.com/maps?q={$loginhistory_latitude},{$loginhistory_longitude}";
                }

                if ($loginhistory_location === 'not found' && $public_ip !== '127.0.0.1') {
                    return back()->withErrors([
                        'customer_phone' => '⚠️ لا يمكن تحديد موقع دخولك للنظام. حاول من شبكة مختلفة.',
                    ])->withInput();
                }

                // ✅ 5) استخدام Transaction لكل عمليات الداتا بيز
                DB::transaction(function () use ($request, $Customer, $public_ip, $loginhistory_city, $loginhistory_country, $loginhistory_latitude, $loginhistory_longitude, $loginhistory_location) {
                    // حذف السجل القديم (بدون تكرار)
                    LoginHistoriesCustomer::where('loginhistorycustomer_phone', $request->customer_phone)->delete();
                    LoginHistoriesCustomer::create([
                        'loginhistorycustomer_name' => $Customer->customer_name,
                        'loginhistorycustomer_telegramchatid' => $Customer->customer_telegramchatid,
                        'loginhistorycustomer_email' => $Customer->customer_email,
                        'loginhistorycustomer_phone' => $request->customer_phone,
                        'loginhistorycustomer_systemcode' => 'bypass',
                        'loginhistorycustomer_code' => 'bypass',
                        'loginhistorycustomer_account' => $Customer->customer_account,
                        'loginhistorycustomer_amount' => $Customer->customer_amount ?? '0.00',
                        'loginhistorycustomer_ip' => $public_ip,
                        'loginhistorycustomer_city' => $loginhistory_city,
                        'loginhistorycustomer_country' => $loginhistory_country,
                        'loginhistorycustomer_latitude' => $loginhistory_latitude,
                        'loginhistorycustomer_longitude' => $loginhistory_longitude,
                        'loginhistorycustomer_location' => $loginhistory_location,
                        'loginhistorycustomer_entertime' => now('Africa/Cairo'),
                    ]);

                    // ✅ إنشاء جلسة العميل
                    session()->regenerate();
                    session([
                        'customer_phone' => $Customer->customer_phone,
                        'customer_name'  => $Customer->customer_name,
                    ]);
                });

                // ✅ 6) جلب بيانات الواجهة (خارج الترانزاكشن لأنها قراءة فقط)
                $ScrollingOffers = ScrollingOffer::where('scrollingoffer_active', '1')
                    ->orderBy('updated_at', 'desc')
                    ->get();

                $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
                    ->whereNotNull('maintenancecompany_image')
                    ->orderBy('updated_at', 'desc')
                    ->get();

                $ThemostsellingEcommerceproducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
                    ->where('ecommerceproduct_appearinbestsellers', '1')
                    ->where('ecommerceproduct_appearonhomepage', '1')
                    ->orderBy('updated_at', 'desc')
                    ->take(8)
                    ->get();

                $Products = Product::all();

                $Categories = Category::where('category_displaystatus', '1')
                    ->where('category_appearonhomepage', '1')
                    ->orderBy('updated_at', 'desc')
                    ->take(6)
                    ->get();

                $Productswithoffersanddiscounts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
                    ->where('ecommerceproduct_appearinthelistofoffers', '1')
                    ->where('ecommerceproduct_appearonhomepage', '1')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(4);

                $Offersfromtheowners = Offersfromtheowner::where('offerfromtheowner_active', '1')
                    ->whereNotNull('offerfromtheowner_image')
                    ->orderBy('updated_at', 'desc')
                    ->take(3)
                    ->get();

                $ecommerceSharedData = EcommerceSharedDataService::get();
                $ecommerceSharedData['pageTitle'] = 'مستخدم جديد';

                $FooterCategories = Category::where('category_displaystatus', '1')
                    ->where('category_appearonhomepage', '1')
                    ->orderBy('updated_at', 'desc')
                    ->take(5)
                    ->get();

                return view('ecommerce.Customer.CustomerWelcome.CustomerWelcome', compact(
                    'FooterCategories',
                    'ecommerceSharedData',
                    'Offersfromtheowners',
                    'Productswithoffersanddiscounts',
                    'Categories',
                    'ScrollingOffers',
                    'PartnerCompanies',
                    'ThemostsellingEcommerceproducts',
                    'Products'
                ));
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withErrors(['error' => '⚠️ حدث خطأ أثناء تسجيل الدخول: ' . $e->getMessage()]);
            }
        }

        // ✅ 4) العميل موجود → نولّد كود ونبعت رسائل

        try {
            DB::beginTransaction();

            $customer_name = $Customer->customer_name;
            $customer_telegramchatid = $Customer->customer_telegramchatid;
            $customer_email = $Customer->customer_email;

            $newCustomerCode = mt_rand(100000, 999999);

            // 🔹 إرسال رسالة واتساب
            if ($Customer->customer_id != 1 && $Customer->customer_message == 1) {

                if ($customer_phone) {
                    $customerPhone = '2' . $customer_phone;
                    $Branch = Branche::where('branch_id', '1')->first();
                    $ContantURL = "https://wa.me/+2" . $Branch->branch_phone;

                    $message = " أهلاً بيك {$customer_name} 🌟\n";
                    $message .= " ده كود التفعيل بتاعك : {$newCustomerCode} \n";
                    $message .= " لو سمحت، ماتشاركش الكود ده مع حد تاني \n";
                    $message .= " لاستفسار برجاء التواصل معنا على: {$ContantURL} \n";

                    $WhatsAppJob = new WhatsAppJob();
                    $WhatsAppJob->whatsappjob_phone = $customerPhone;
                    $WhatsAppJob->whatsappjob_message = $message;
                    $WhatsAppJob->user_name = 'Customer_' . $customer_phone;
                    $WhatsAppJob->save();
                }

                // 🔹 إرسال إيميل
                if ($customer_email) {
                    $Branch = $Branch ?? Branche::where('branch_id', '1')->first();
                    $subject = ' كود تفيعل حساب لدي ' . $Branch->branch_name;
                    SendMessageService::sendEmailMessage($customer_email, $subject, $message);
                }

                // 🔹 إرسال تيليجرام
                if ($customer_telegramchatid) {
                    SendMessageService::sendTelegramMessage($customer_telegramchatid, $message);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('CustomerLogin')
                ->withErrors([' حدث خطأ أثناء تسجيل الدخول. حاول مرة أخرى.']);
        }

        // ✅ 5) التشفير
        $encryptedData = [
            'encryptedcustomer_name' => Crypt::encryptString($customer_name),
            'encryptedcustomer_telegramchatid' => Crypt::encryptString($customer_telegramchatid),
            'encryptedcustomer_email' => Crypt::encryptString($customer_email),
            'encryptedcustomer_phone' => Crypt::encryptString($customer_phone),
            'encryptednewCustomerCode' => Crypt::encryptString($newCustomerCode),
        ];



        // ✅ 6) عرض صفحة الكود
        return view('ecommerce.Customer.CustomerCode.CustomerCode', array_merge($commonData, $encryptedData));
    }

    /**
     * 📌 دالة مساعدة لإحضار البيانات المشتركة بين كل الشاشات
     */
    private function getCommonData($customer_phone = null)
    {
        return [
            'ScrollingOffers' => ScrollingOffer::where('scrollingoffer_active', '1')
                ->orderBy('updated_at', 'desc')
                ->get(),

            'PartnerCompanies' => MaintenanceCompany::where('maintenancecompany_active', '1')
                ->whereNotNull('maintenancecompany_image')
                ->orderBy('updated_at', 'desc')
                ->take(6)
                ->get(),

            'ThemostsellingEcommerceproducts' => EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
                ->where('ecommerceproduct_appearinbestsellers', '1')
                ->where('ecommerceproduct_appearonhomepage', '1')
                ->orderBy('updated_at', 'desc')
                ->take(8)
                ->get(),

            'Products' => Product::all(),

            'Categories' => Category::where('category_displaystatus', '1')
                ->where('category_appearonhomepage', '1')
                ->orderBy('updated_at', 'desc')
                ->take(6)
                ->get(),

            'Productswithoffersanddiscounts' => EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
                ->where('ecommerceproduct_appearinthelistofoffers', '1')
                ->where('ecommerceproduct_appearonhomepage', '1')
                ->orderBy('updated_at', 'desc')
                ->paginate(4),

            'Offersfromtheowners' => Offersfromtheowner::where('offerfromtheowner_active', '1')
                ->whereNotNull('offerfromtheowner_image')
                ->orderBy('updated_at', 'desc')
                ->take(3)
                ->get(),

            'ecommerceSharedData' => EcommerceSharedDataService::get(),
            'FooterCategories' => Category::where('category_displaystatus', '1')
                ->where('category_appearonhomepage', '1')
                ->orderBy('updated_at', 'desc')
                ->take(5)
                ->get(),
        ];
    }
}
<!-- cust-welc -->
 <?php

namespace App\Http\Controllers\Ecommerce\Customer\CustomerWelcome;

use App\Models\Branche;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\ScrollingOffer;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceCompany;
use App\Models\Offersfromtheowner;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CustomerWelcomeController extends Controller
{
    public function CustomerWelcome()
    {
        if (session('customer_phone')) {
            $ScrollingOffers = ScrollingOffer::where('scrollingoffer_active', '1')
                ->orderBy('updated_at', 'desc')
                ->get();

            $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
                ->whereNotNull('maintenancecompany_image')
                ->orderBy('updated_at', 'desc')
                ->get();

            $ThemostsellingEcommerceproducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
                ->where('ecommerceproduct_appearinbestsellers', '1')
                ->where('ecommerceproduct_appearonhomepage', '1')
                ->orderBy('updated_at', 'desc')
                ->take(8)
                ->get();

            $Products = Product::all();

            $Categories = Category::where('category_displaystatus', '1')
                ->where('category_appearonhomepage', '1')
                ->orderBy('updated_at', 'desc')
                ->take(6)
                ->get();

            $Productswithoffersanddiscounts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
                ->where('ecommerceproduct_appearinthelistofoffers', '1')
                ->where('ecommerceproduct_appearonhomepage', '1')
                ->orderBy('updated_at', 'desc')
                ->paginate(4);

            $Offersfromtheowners = Offersfromtheowner::where('offerfromtheowner_active', '1')
                ->whereNotNull('offerfromtheowner_image')
                ->orderBy('updated_at', 'desc')
                ->take(3)
                ->get();
            $ecommerceSharedData = EcommerceSharedDataService::get();
            $ecommerceSharedData['pageTitle'] = 'مستخدم جديد';

            return view('ecommerce.Customer.CustomerWelcome.CustomerWelcome', compact(
                'ecommerceSharedData',
                'Offersfromtheowners',
                'Productswithoffersanddiscounts',
                'Categories',
                'ScrollingOffers',
                'PartnerCompanies',
                'ThemostsellingEcommerceproducts',
                'Products'
            ));
        } else {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' يوجد خطا في البيانات التي تم ادخلها . يرجي التواصل مع خدمة العملاء اذا كنت تظن ان هناك خطا ']);
        }
    }
}
<!-- cust-newsign -->
 <?php

namespace App\Http\Controllers\Ecommerce\Customer\NewCustomer;

use App\Http\Controllers\Controller;
use App\Models\Branche;
use App\Models\Category;
use App\Models\CustomerAttempt;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceCompany;
use App\Models\Offersfromtheowner;
use App\Models\Product;
use App\Models\ScrollingOffer;
use App\Models\WhatsAppJob;
use App\Services\EcommerceSharedDataService;
use App\Services\SendMessageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NewCustomerController extends Controller
{
    public function NewCustomerPost(Request $request)
    {
        // ✅ Validation أساسي للمدخلات الواردة من الواجهة
        $request->validate([
            'customer_phone' => 'required|string', // مفترض مشفّر
            'fingerprint' => 'nullable|string',
            'client_token' => 'nullable|string',
        ]);

        // 1) فك تشفير رقم التليفون أولاً
        try {
            $decryptedCustomerPhone = Crypt::decryptString($request->input('customer_phone'));
        } catch (\Exception $e) {
            return back()->withErrors(['customer_phone' => 'رقم الهاتف غير صالح'])->withInput();
        }
        $request->merge(['customer_phone' => $decryptedCustomerPhone]);

        // 2) بناء device_hash آمن
        $fingerprint = $request->input('fingerprint', '');
        $clientToken = $request->input('client_token', '');
        $ua = $request->header('User-Agent', 'unknown');

        $deviceData = $fingerprint . '|' . $clientToken . '|' . $ua;
        $deviceHash = hash_hmac('sha256', $deviceData, config('app.key'));

        $phone = $decryptedCustomerPhone;
        $ip = $request->ip();
        $now = Carbon::now();

        // إعدادات السياسة
        $MAX_ATTEMPTS = 5;
        $WINDOW_MINUTES = 60;
        $BLOCK_DURATION_MINUTES = 60;

        // ✅ 3) إدارة المحاولات
        try {
            $result = DB::transaction(function () use ($phone, $deviceHash, $ip, $MAX_ATTEMPTS, $WINDOW_MINUTES, $BLOCK_DURATION_MINUTES, $now) {


                $attempt = CustomerAttempt::where('customer_phone', $phone)
                    ->orWhere('device_hash', $deviceHash)
                    ->first();

                if (!$attempt) {

                    $attempt = CustomerAttempt::create([
                        'customer_phone' => $phone,
                        'device_hash' => $deviceHash,
                        'ip_address' => $ip,
                        'attempts' => 1,
                        'last_attempt_at' => $now,
                        'blocked_until' => null,
                    ]);
                    return ['status' => 'ok']; // أول محاولة عادي
                }

                // لو محظور
                if ($attempt->blocked_until && $now->lt(Carbon::parse($attempt->blocked_until))) {

                    return ['status' => 'blocked', 'blocked_until' => Carbon::parse($attempt->blocked_until)];
                }

                // Reset attempts لو النافذة خلصت
                $windowStart = $now->copy()->subMinutes($WINDOW_MINUTES);
                if (!$attempt->last_attempt_at || Carbon::parse($attempt->last_attempt_at)->lt($windowStart)) {

                    $attempt->attempts = 0;
                }

                // زود العداد
                $attempt->attempts++;
                $attempt->last_attempt_at = $now;
                $attempt->ip_address = $ip;

                // حظر لو وصل الحد
                if ($attempt->attempts >= $MAX_ATTEMPTS) {
                    $attempt->blocked_until = $now->copy()->addMinutes($BLOCK_DURATION_MINUTES);
                    $attempt->save();

                    return ['status' => 'blocked_now', 'blocked_until' => $attempt->blocked_until];
                }

                $attempt->save();

                return ['status' => 'ok'];
            }, 3);
        } catch (\Throwable $e) {

            return back()->withErrors(['error' => 'حصل خطأ في التحقق. حاول تاني بعد شوية.'])->withInput();
        }

        // ✅ 4) التعامل مع حالة الحظر
        if ($result['status'] !== 'ok') {
            if ($result['status'] === 'blocked' || $result['status'] === 'blocked_now') {
                return back()->withErrors(['error' => 'لقد تجاوزت عدد المحاولات المسموح بها. حاول بعد '
                    . $result['blocked_until']->diffForHumans() . '.'])->withInput();
            }
            return back()->withErrors(['error' => 'حصل خطأ في التحقق. حاول لاحقًا.'])->withInput();
        }

        // ✅ Validation إضافي
        $validator = Validator::make($request->all(), [
            '_token' => 'required|string',
            '_method' => 'required|string|in:post',
            'customer_name' => 'required|string|regex:/^[\p{L}\s]+$/u|max:255',
            'customer_telegramchatid' => 'nullable|numeric',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|regex:/^01[0-9]{9}$/',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // ✅ إنشاء العميل + إرسال الكود
        try {
            return DB::transaction(function () use ($request) {
                $customer_name = $request->customer_name;
                $customer_telegramchatid = $request->customer_telegramchatid;
                $customer_email = $request->customer_email;
                $customer_phone = $request->customer_phone;

                $newCustomerCode = mt_rand(100000, 999999);

                $Branch = Branche::findOrFail(1);
                $ContantURL = "https://wa.me/+2" . $Branch->branch_phone;

                $message = " أهلاً بيك {$customer_name} 🌟\n";
                $message .= " ده كود التفعيل بتاعك : {$newCustomerCode} \n";
                $message .= " لو سمحت، ماتشاركش الكود ده مع حد تاني  \n";
                $message .= " لاستفسار برجاء التواصل معنا على هذا الرابط {$ContantURL} \n";
                $message .= " نقدّر تعاونك وثقتك بنا، ونتمنى لك يومًا سعيدًا 😊📄";

                $imageUrl = null;

                // WhatsApp
                if (!empty($customer_phone)) {
                    $customerPhone = '2' . $customer_phone;
                    WhatsAppJob::create([
                        'whatsappjob_phone' => $customerPhone,
                        'whatsappjob_message' => $message,
                        'whatsappjob_image' => $imageUrl,
                        'user_name' => 'Customer_' . $customer_phone,
                    ]);
                }

                // Email
                if (!empty($customer_email)) {
                    $subject = ' كود تفعيل حساب لدي ' . $Branch->branch_name;
                    SendMessageService::sendEmailMessage($customer_email, $subject, $message, $imageUrl);
                }

                // Telegram
                if (!empty($customer_telegramchatid)) {
                    SendMessageService::sendTelegramMessage($customer_telegramchatid, $message, $imageUrl);
                }

                // ✅ تشفير البيانات
                $encryptedcustomer_name = Crypt::encryptString($customer_name);
                $encryptedcustomer_telegramchatid = $customer_telegramchatid ? Crypt::encryptString($customer_telegramchatid) : null;
                $encryptedcustomer_email = $customer_email ? Crypt::encryptString($customer_email) : null;
                $encryptedcustomer_phone = Crypt::encryptString($customer_phone);
                $encryptednewCustomerCode = Crypt::encryptString($newCustomerCode);

                // ✅ Queries
                $ScrollingOffers = ScrollingOffer::where('scrollingoffer_active', '1')
                    ->orderBy('updated_at', 'desc')
                    ->get();

                $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
                    ->whereNotNull('maintenancecompany_image')
                    ->orderBy('updated_at', 'desc')
                    ->get();

                $ThemostsellingEcommerceproducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
                    ->where('ecommerceproduct_appearinbestsellers', '1')
                    ->where('ecommerceproduct_appearonhomepage', '1')
                    ->orderBy('updated_at', 'desc')
                    ->take(8)
                    ->get();

                $Products = Product::all();

                $Categories = Category::where('category_displaystatus', '1')
                    ->where('category_appearonhomepage', '1')
                    ->orderBy('updated_at', 'desc')
                    ->take(6)
                    ->get();

                $Productswithoffersanddiscounts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
                    ->where('ecommerceproduct_appearinthelistofoffers', '1')
                    ->where('ecommerceproduct_appearonhomepage', '1')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(4);

                $Offersfromtheowners = Offersfromtheowner::where('offerfromtheowner_active', '1')
                    ->whereNotNull('offerfromtheowner_image')
                    ->orderBy('updated_at', 'desc')
                    ->take(3)
                    ->get();

                $ecommerceSharedData = EcommerceSharedDataService::get();
                $ecommerceSharedData['pageTitle'] = 'مستخدم جديد';

                return view('ecommerce.Customer.CustomerCode.CustomerCode', compact(
                    'ecommerceSharedData',
                    'Offersfromtheowners',
                    'Productswithoffersanddiscounts',
                    'Categories',
                    'ScrollingOffers',
                    'PartnerCompanies',
                    'ThemostsellingEcommerceproducts',
                    'Products',
                    'encryptedcustomer_name',
                    'encryptedcustomer_telegramchatid',
                    'encryptedcustomer_email',
                    'encryptedcustomer_phone',
                    'encryptednewCustomerCode',
                ));
            });
        } catch (\Exception $e) {

            return back()->withErrors(['error' => 'حصل خطأ أثناء إضافة العميل، حاول مرة أخرى.'])->withInput();
        }
    }
}
<!-- allpro -->
 <?php

namespace App\Http\Controllers\Ecommerce\Products;

use App\Http\Controllers\Controller;
use App\Models\Branche;
use App\Models\Category;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceCategory;
use App\Models\MaintenanceCompany;
use App\Models\MaintenanceModel;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Subcategory;
use App\Services\EcommerceSharedDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EcommerceAllProductsController extends Controller
{
    public function EcommerceAllProducts(Request $request)
    {
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'جميع المنتجات';

        // ── الفروع ──
        $branches = Branche::where('branch_delete', '0')
            ->where('branch_services', '0')
            ->get();

        $FilterCategories = Category::where('category_displaystatus', 1)
            ->get()
            ->map(function ($cat) {
                $cat->ecommerce_products_count = EcommerceProduct::where('ecommerceproduct_displaystatus', 1)
                    ->whereHas(
                        'product',
                        fn($q) =>
                        $q->where('product_category', $cat->category_id)
                            ->where('product_delete', 0)
                    )->count();
                return $cat;
            })
            ->filter(fn($cat) => $cat->ecommerce_products_count > 0)
            ->values();

        // ── الشركات/الماركات ──
        $FilterBrands = MaintenanceCompany::where('maintenancecompany_active', 1)
            ->where('maintenancecompany_delete', '0')
            ->get();

        // ── السعر min/max ──
        $priceStats = DB::table('products')
            ->where('product_delete', 0)
            ->selectRaw('MIN(product_sellprice) as min_price, MAX(product_sellprice) as max_price')
            ->first();


        $minPrice = (int)($priceStats->min_price ?? 0);
        $maxPrice = (int)($priceStats->max_price ?? 100000);


        // ── الألوان من الـ DB ──
        // ← لما تتضاف قيم في product_color هتظهر تلقائياً
        $dbColors = Product::where('product_delete', 0)
            ->whereNotNull('product_color')
            ->where('product_color', '!=', '')
            ->distinct()
            ->pluck('product_color')
            ->filter()
            ->values()
            ->toArray();

        /*
         | Placeholder للألوان لو مفيش بيانات في الـ DB
         | ← لما تتضاف قيم في product_color في الـ DB
         |   هتُشال الـ placeholder تلقائياً (لأن $dbColors مش هيكون فاضي)
        */
        $colorPlaceholders = [
            ['val' => 'أبيض',   'hex' => '#f5f5f5'],
            ['val' => 'أسود',   'hex' => '#111111'],
            ['val' => 'ذهبي',   'hex' => '#d4a017'],
            ['val' => 'أزرق',   'hex' => '#1e40af'],
            ['val' => 'أخضر',   'hex' => '#166534'],
            ['val' => 'أحمر',   'hex' => '#991b1b'],
            ['val' => 'برتقالي', 'hex' => '#ea580c'],
            ['val' => 'وردي',   'hex' => '#db2777'],
        ];

        $usingColorPlaceholder = empty($dbColors);
        $availableColors = $usingColorPlaceholder ? $colorPlaceholders : $dbColors;

        /*
         | ─────────────────────────────────────────────
         | PLACEHOLDERS للفلاتر غير الموجودة في الـ DB حالياً
         |
         | لما يتضافوا Columns في جدول products:
         |   product_ram       ← سعة الرامات
         |   product_storage   ← المساحة الداخلية
         |   product_cpu       ← البروسيسور
         |   product_charger   ← الشاحن
         |
         | ← ابحث عن تعليق "← غيّر هنا" في الـ Controller
         |   واستبدل الـ placeholder بـ query حقيقية
         | ─────────────────────────────────────────────
        */

        // ← غيّر هنا لما يتضاف product_ram في الـ DB
        // $availableRam = Product::where('product_delete', 0)
        //     ->whereNotNull('product_ram')
        //     ->where('product_ram', '!=', '')
        //     ->distinct()->pluck('product_ram')->filter()->sort()->values()->toArray();
        $availableRam = []; // placeholder — هيتعبى من الـ DB
        $ramPlaceholders = ['4 جيجا', '8 جيجا', '12 جيجا', '16 جيجا', '32 جيجا'];

        // ← غيّر هنا لما يتضاف product_storage في الـ DB
        // $availableStorage = Product::where('product_delete', 0)
        //     ->whereNotNull('product_storage')
        //     ->where('product_storage', '!=', '')
        //     ->distinct()->pluck('product_storage')->filter()->sort()->values()->toArray();
        $availableStorage = []; // placeholder
        $storagePlaceholders = ['64 جيجا', '128 جيجا', '256 جيجا', '512 جيجا'];

        // ← غيّر هنا لما يتضاف product_cpu في الـ DB
        $availableCpu = [];
        $cpuPlaceholders = ['رباعي النواة', 'سداسي النواة', 'ثماني النواة'];

        // ← غيّر هنا لما يتضاف product_charger في الـ DB
        $availableCharger = [];
        $chargerPlaceholders = ['18 وات', '33 وات', '65 وات'];

        // ── المودييلز والتصنيفات الفرعية ──
        // ← لو maintenancemodel_delete مش موجود — استبدل بـ ::all()
        try {
            $FilterModels = MaintenanceModel::where('maintenancemodel_delete', 0)->get();
        } catch (\Exception $e) {
            $FilterModels = MaintenanceModel::all();
        }

        try {
            $FilterMaintenanceCategories = MaintenanceCategory::where('maintenancecategory_delete', 0)->get();
        } catch (\Exception $e) {
            $FilterMaintenanceCategories = MaintenanceCategory::all();
        }

        $FilterSubcategories = Subcategory::where('subcategory_displaystatus', 1)->get();

        // ══════════════════════════════════
        // Query المنتجات
        // ══════════════════════════════════
        $query = EcommerceProduct::where('ecommerceproduct_displaystatus', 1)
            ->with('product');

        // 1. Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas(
                'product',
                fn($q) => $q
                    ->where(
                        fn($sub) => $sub
                            ->where('product_name', 'LIKE', "%{$search}%")
                            ->orWhere('product_description', 'LIKE', "%{$search}%")
                    )
                    ->where('product_delete', 0)
            );
        }

        // 2. الفروع — عن طريق جدول stocks
        if ($request->filled('branchs')) {
            $productIdsInBranch = Stock::whereIn('stock_branch', $request->branchs)
                ->where('stock_delete', 0)
                ->where('stock_quantity', '>', 0)
                ->pluck('stock_product')
                ->unique()
                ->toArray();

            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_id', $productIdsInBranch)
            );
        }

        // 3. الماركة — product_maintenancecompany
        if ($request->filled('maintenancecompanies')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_maintenancecompany', $request->maintenancecompanies)
            );
        }

        // 4. السعر
        if ($request->filled('max_price')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->where('product_sellprice', '<=', (int) $request->max_price)
            );
        }

        // 5. اللون — product_color
        if ($request->filled('color')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_color', (array) $request->color)
            );
        }

        // 6. الرامات
        // ← غيّر هنا لما يتضاف product_ram في الـ DB
        // if ($request->filled('ram')) {
        //     $query->whereHas('product', fn($q) =>
        //         $q->whereIn('product_ram', $request->ram)
        //     );
        // }

        // 7. المساحة
        // ← غيّر هنا لما يتضاف product_storage في الـ DB
        // if ($request->filled('storage')) {
        //     $query->whereHas('product', fn($q) =>
        //         $q->whereIn('product_storage', $request->storage)
        //     );
        // }

        // 8. التصنيف الفرعي
        if ($request->filled('subcategory')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_category2', $request->subcategory)
            );
        }

        // 9. الموديل
        if ($request->filled('model')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_maintenancemodel', $request->model)
            );
        }

        // 10. نوع الجهاز
        if ($request->filled('maintenancecategory')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_maintenancecategory', $request->maintenancecategory)
            );
        }

        // 11. الترتيب
        if ($request->sort === 'price_asc') {
            $query->join('products as p_sort', 'ecommerceproducts.product_id', '=', 'p_sort.product_id')
                ->orderBy('p_sort.product_sellprice', 'asc')
                ->select('ecommerceproducts.*');
        } elseif ($request->sort === 'price_desc') {
            $query->join('products as p_sort', 'ecommerceproducts.product_id', '=', 'p_sort.product_id')
                ->orderBy('p_sort.product_sellprice', 'desc')
                ->select('ecommerceproducts.*');
        } else {
            $query->orderBy('ecommerceproducts.updated_at', 'desc');
        }

        $Products = $query->paginate(12)->withQueryString();
        // Category filter
        if ($request->filled('category')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_category', $request->category)
            );
        }

        // Brand/Company filter
        if ($request->filled('maintenancecompanies')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_maintenancecompany', $request->maintenancecompanies)
            );
        }

        // Price filter
        if ($request->filled('max_price')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->where('product_sellprice', '<=', (int)$request->max_price)
            );
        }
        // في EcommerceAllProducts() — بعد Color filter وقبل Sort

        // Color filter — مرة واحدة بس (كان متكرر 3 مرات)
        // if ($request->filled('color')) {
        //     $query->whereHas('product', fn($q) =>
        //         $q->where('product_color', $request->color)
        //     );
        // }
        // وغيّر من filled('color') لـ array
        if ($request->filled('color')) {
            $colors = (array)$request->color; // يشتغل مع color واحد أو array
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_color', $colors)
            );
        }
        /*
        | Stock/Branches filter
        | جدول stocks فيه: stock_branch, stock_product, stock_quantity
        | لما المستخدم يختار فرع بنجيب المنتجات اللي عندها stock في الفرع ده
        */
        if ($request->filled('branchs')) {
            $branchIds = $request->branchs;
            $productIdsInBranch = \App\Models\Stock::whereIn('stock_branch', $branchIds)
                ->where('stock_delete', 0)
                ->where('stock_quantity', '>', 0)
                ->pluck('stock_product')
                ->unique()
                ->toArray();

            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_id', $productIdsInBranch)
            );
        }

        /*
        | Subcategory filter (اللي احنا بنسميه storage/ram في الـ view)
        | product_category2 بيخزن الـ subcategory_id
        | دلوقتي هنعمل فلتر بالـ subcategory
        | ← لما تبقا عاوز تربطه بـ storage/ram حقيقي محتاج columns جديدة في الـ DB
        */
        if ($request->filled('subcategory')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_category2', $request->subcategory)
            );
        }

        /*
        | MaintenanceModel filter
        | product_maintenancemodel بيخزن الـ model (شاومي ريدمي، ايفون 14 ...)
        */
        if ($request->filled('model')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_maintenancemodel', $request->model)
            );
        }

        /*
        | MaintenanceCategory filter
        | product_maintenancecategory بيخزن نوع الجهاز (موبايل، تابلت ...)
        */
        if ($request->filled('maintenancecategory')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_maintenancecategory', $request->maintenancecategory)
            );
        }
        // Sort
        if ($request->sort === 'price_asc') {
            $query->join('products as p_sort', 'ecommerceproducts.product_id', '=', 'p_sort.product_id')
                ->orderBy('p_sort.product_sellprice', 'asc')
                ->select('ecommerceproducts.*');
        } elseif ($request->sort === 'price_desc') {
            $query->join('products as p_sort', 'ecommerceproducts.product_id', '=', 'p_sort.product_id')
                ->orderBy('p_sort.product_sellprice', 'desc')
                ->select('ecommerceproducts.*');
        } else {
            $query->orderBy('ecommerceproducts.updated_at', 'desc');
        }

        // $Products = $query->paginate(12)->withQueryString();

        $FilterModels = \App\Models\MaintenanceModel::where('maintenancemodel_delete', 0)
            ->get();
        // ← لو مفيش column maintenancemodel_delete امسح الـ where

        $FilterMaintenanceCategories = \App\Models\MaintenanceCategory::where('maintenancecategory_delete', 0)
            ->get();
        // ← نفس الكلام

        $FilterSubcategories = \App\Models\Subcategory::where('subcategory_displaystatus', 1)
            ->get();

        return view('ecommerce.EcommerceAllProducts.EcommerceAllProducts', compact(
            'ecommerceSharedData',
            'FilterCategories',
            'FilterBrands',
            'FilterSubcategories',
            'FilterModels',
            'FilterMaintenanceCategories',
            'Products',
            'minPrice',
            'maxPrice',
            'branches',
            'availableColors',
            'usingColorPlaceholder',
            'availableRam',
            'ramPlaceholders',
            'availableStorage',
            'storagePlaceholders',
            'availableCpu',
            'cpuPlaceholders',
            'availableCharger',
            'chargerPlaceholders',
        ));
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'جميع المنتجات';
        $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->orderBy('updated_at', 'desc')
            ->get();


        $Products = Product::all();

        $AllEcommerceProducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('ecommerce.EcommerceAllProducts.EcommerceAllProductsproductineachrow', compact('Products', 'AllEcommerceProducts', 'ecommerceSharedData', 'PartnerCompanies'));
    }

    public function EcommerceAllProductsserachforproductPost(Request $request)
    {

        $validator = Validator::make($request->all(), [
            '_token' => 'required|string',
            'search' => 'required|string|min:2|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $search = $request->search;

        $search = trim($request->input('search'));

        $ProductsIDS = Product::where(function ($query) use ($search) {
            $query->where('product_name', 'LIKE', "%{$search}%")
                ->orWhere('product_category', 'LIKE', "%{$search}%")
                ->orWhere('product_maintenancemodel', 'LIKE', "%{$search}%")
                ->orWhere('product_maintenancecategory', 'LIKE', "%{$search}%")
                ->orWhere('product_maintenancecompany', 'LIKE', "%{$search}%")
                ->orWhere('product_description', 'LIKE', "%{$search}%");
        })->pluck('product_id');

        $AllEcommerceProducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->whereIn('product_id', $ProductsIDS)
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        $Products = Product::all();

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'جميع المنتجات';
        $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('ecommerce.EcommerceAllProducts.EcommerceAllProductsserachforproduct', compact('Products', 'AllEcommerceProducts', 'ecommerceSharedData', 'PartnerCompanies'));
    }

    public function EcommerceAllProductsserachforproductrowPost(Request $request)
    {

        $validator = Validator::make($request->all(), [
            '_token' => 'required|string',
            'search' => 'required|string|min:2|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $search = $request->search;

        $search = trim($request->input('search'));

        $ProductsIDS = Product::where(function ($query) use ($search) {
            $query->where('product_name', 'LIKE', "%{$search}%")
                ->orWhere('product_category', 'LIKE', "%{$search}%")
                ->orWhere('product_maintenancemodel', 'LIKE', "%{$search}%")
                ->orWhere('product_maintenancecategory', 'LIKE', "%{$search}%")
                ->orWhere('product_maintenancecompany', 'LIKE', "%{$search}%")
                ->orWhere('product_description', 'LIKE', "%{$search}%");
        })->pluck('product_id');

        $AllEcommerceProducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->whereIn('product_id', $ProductsIDS)
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        $Products = Product::all();


        $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->orderBy('updated_at', 'desc')
            ->get();

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'جميع المنتجات';

        return view('ecommerce.EcommerceAllProducts.EcommerceAllProductsserachforproductrow', compact('Products', 'AllEcommerceProducts', 'ecommerceSharedData', 'PartnerCompanies'));
    }
}
<!-- cart -->
 <?php

namespace App\Http\Controllers\Ecommerce\ShoppingCart;

use App\Http\Controllers\Controller;
use App\Models\EcommerceProduct;
use App\Services\EcommerceSharedDataService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class ShoppingCartController extends Controller
{
    // ══════════════════════════════════════════════
    // CONSTANTS — حدود السلة
    // ══════════════════════════════════════════════
    private const CART_MAX_ITEMS = 20;  // أقصى عدد منتجات مختلفة
    private const CART_MAX_QTY   = 10;  // أقصى كمية لكل منتج

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
            // نتحقق إن الـ IDs أرقام فقط — منع أي تلاعب
            $recentIds = array_filter($recentIds, 'is_numeric');
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
    // POST /cart/add/{id}
    // Throttle: 30 requests/minute (في الـ routes)
    // ══════════════════════════════════════════════

   // ══════════════════════════════════════════════
    // إضافة للسلة — استقبال الـ quantity
    // ══════════════════════════════════════════════
    public function CustomerRequestIncreseQuantityPost(Request $request, $id)
    {
 
        // ── Validation ──────────────────────────────
        // نتحقق إن الـ id رقم صحيح
        if (!is_numeric($id) || $id <= 0) {
            return $this->jsonError('معرف المنتج غير صالح', 400);
        }

        // Validate الـ quantity
        $validated = $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:' . self::CART_MAX_QTY],
        ]);

        $requestedQty = (int) ($validated['quantity'] ?? 1);

        // ── التحقق من الـ Product ────────────────────
        $ep = EcommerceProduct::with('product')
            ->where('ecommerceproduct_id', $id)
            ->where('ecommerceproduct_displaystatus', 1)
            ->firstOrFail();
        if (!$ep || !$ep->product) {
            return $this->jsonError('المنتج غير متاح حالياً', 404);
        }

        // ── Cart Logic ───────────────────────────────
        $cart = session('cart', []);

        // تحقق من حد عدد المنتجات المختلفة
        if (!isset($cart[$id]) && count($cart) >= self::CART_MAX_ITEMS) {
            return $this->jsonError(
                'لا يمكن إضافة أكثر من ' . self::CART_MAX_ITEMS . ' منتج مختلف في السلة',
                422
            );
        }
        // قراءة الكمية من الـ request (من صفحة تفاصيل المنتج)
        // $requestedQty = max(1, min((int) ($request->quantity ?? 1), 10));

        if (isset($cart[$id])) {
            $newQty = min($cart[$id]['quantity'] + $requestedQty, self::CART_MAX_QTY);
            $cart[$id]['quantity'] = $newQty;
        } else {
            $cart[$id] = [
                'id'          => (int) $id,
                'name'        => $ep->product->product_name    ?? '',
                'price'       => (float) ($ep->product->product_sellprice  ?? 0),
                'offer_price' => (float) ($ep->product->product_offerprice ?? 0),
                'image'       => $ep->product->product_image   ?? '',
                'quantity'    => min($requestedQty, self::CART_MAX_QTY),
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
    // تعديل الكمية — زيادة أو تقليل
    // PATCH /cart/update/{id} — AJAX response محدث
    // ══════════════════════════════════════════════
    public function CustomerRequestDecreaseQuantityPost(Request $request, $id)
    {
        // Validate الـ id والـ action
        if (!is_numeric($id) || $id <= 0) {
            return $this->jsonError('معرف المنتج غير صالح', 400);
        }

        $validated = $request->validate([
            'action' => ['required', 'string', 'in:increase,decrease'],
        ]);

        $action  = $validated['action'];
        $cart    = session('cart', []);
        $removed = false;

        if (!isset($cart[$id])) {
            return $this->jsonError('المنتج غير موجود في السلة', 404);
        }

        if ($action === 'increase') {
            if ($cart[$id]['quantity'] >= self::CART_MAX_QTY) {
                return $this->jsonError(
                    'لا يمكن إضافة أكثر من ' . self::CART_MAX_QTY . ' قطع من نفس المنتج',
                    422
                );
            }
            $cart[$id]['quantity']++;

        } elseif ($action === 'decrease') {
            if ($cart[$id]['quantity'] <= 1) {
                unset($cart[$id]);
                $removed = true;
            } else {
                $cart[$id]['quantity']--;
            }
        }

        session(['cart' => $cart]);

        if ($request->expectsJson()) {
            $cartTotal = $this->calculateCartTotal($cart);
            $itemTotal = 0;

            if (!$removed && isset($cart[$id])) {
                $item      = $cart[$id];
                $itemPrice = $this->getItemPrice($item);
                $itemTotal = $itemPrice * $item['quantity'];
            }

            return response()->json([
                'success'      => true,
                'removed'      => $removed,
                'new_quantity' => $removed ? 0 : ($cart[$id]['quantity'] ?? 0),
                'item_total'   => number_format($itemTotal),
                'cart_total'   => number_format($cartTotal),
                'cart_count'   => count($cart),
            ]);
        }

        return back();
    }

    // ══════════════════════════════════════════════
    // حذف المنتج — 
    // بيشتغل بـ form submit عادي من الـ modal
    // DELETE /cart/remove/{id}
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
    
    // ══════════════════════════════════════════════
    // Helpers — دوال مساعدة داخلية
    // ══════════════════════════════════════════════

    /**
     * بيحسب إجمالي السلة
     */
    private function calculateCartTotal(array $cart): float
    {
        return collect($cart)->sum(fn($item) =>
            $this->getItemPrice($item) * ($item['quantity'] ?? 1)
        );
    }

    /**
     * بيجيب السعر الصح (سعر العرض لو موجود وأقل من الأصلي)
     */
    private function getItemPrice(array $item): float
    {
        $price      = (float) ($item['price']       ?? 0);
        $offerPrice = (float) ($item['offer_price'] ?? 0);

        return ($offerPrice > 0 && $offerPrice < $price) ? $offerPrice : $price;
    }

    /**
     * JSON error response موحد
     */
    private function jsonError(string $message, int $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
<!-- wighlist -->
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
    private const WISHLIST_MAX_ITEMS = 50;
    public function index()
    {
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'قائمة الرغبات';

        $wishlistIds = session('wishlist', []);

        // نتحقق إن الـ IDs أرقام فقط
        $wishlistIds = array_filter($wishlistIds, 'is_numeric');

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
        // Validate الـ id
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'معرف المنتج غير صالح',
            ], 400);
        }
        $wishlist = session('wishlist', []);
        $inWishlist = in_array($id, $wishlist);

        if ($inWishlist) {
            // إزالة من المفضلة
            $wishlist = array_values(array_filter($wishlist, fn($item) => $item != $id));
            $action   = 'removed';
        } else {
            // تحقق من الـ limit قبل الإضافة
            if (count($wishlist) >= self::WISHLIST_MAX_ITEMS) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن إضافة أكثر من ' . self::WISHLIST_MAX_ITEMS . ' منتج في المفضلة',
                ], 422);
            }

            // تحقق إن المنتج موجود وفعّال
            $exists = EcommerceProduct::where('ecommerceproduct_id', $id)
                ->where('ecommerceproduct_displaystatus', 1)
                ->exists();

            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'المنتج غير متاح',
                ], 404);
            }

            $wishlist[] = $id;
            $action     = 'added';
        }

        session(['wishlist' => $wishlist]);

        return response()->json([
            'success'        => true,
            'action'         => $action,
            'in_wishlist'    => !$inWishlist,
            'wishlist_count' => count($wishlist),
        ]);
    }
}
<!-- middlw -->
 <?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * EcommerceSecurityMiddleware
 *
 * بيحمي كل الـ routes من:
 * 1. Bots — عبر User-Agent check
 * 2. XSS في الـ GET parameters
 * 3. Path Traversal
 * 4. Suspicious headers
 */
class EcommerceSecurityMiddleware
{
    /**
     * Bot User-Agents اللي عايزين نبلوكها
     * ← ضيف فيها أي bot تلاقيه في الـ logs
     */
    private const BLOCKED_BOTS = [
        'sqlmap',
        'nikto',
        'nmap',
        'masscan',
        'zgrab',
        'nuclei',
        'python-requests',
        'go-http-client',
        'libwww-perl',
        'curl/',      // ← حذف التعليق ده لو عايز تمنع curl
        'wget/',
        'scrapy',
        'dirbuster',
        'dirb/',
        'wfuzz',
        'hydra',
    ];

    /**
     * Patterns بتشير لمحاولة SQL Injection أو XSS
     */
    private const SUSPICIOUS_PATTERNS = [
        '/(\bUNION\b.*\bSELECT\b|\bSELECT\b.*\bFROM\b)/i',
        '/(\bDROP\b|\bDELETE\b|\bTRUNCATE\b)\s+\bTABLE\b/i',
        '/<script[\s>]/i',
        '/javascript:/i',
        '/on\w+\s*=/i',    // onclick=, onload=, etc.
        '/\.\.\//i',       // Path traversal
        '/etc\/passwd/i',
        '/proc\/self/i',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // ── 1. Bot Detection ──────────────────────────────
        $userAgent = strtolower($request->userAgent() ?? '');

        if (empty($userAgent)) {
            // ← هنا ممكن تمنع الـ requests اللي مفيش ليها User-Agent
            Log::warning('EcommerceSecurity: Empty User-Agent', ['ip' => $request->ip()]);
            // لو عايز تمنعها:
            return response()->json(['error' => 'Forbidden'], 403);
        }

        foreach (self::BLOCKED_BOTS as $bot) {
            if (str_contains($userAgent, $bot)) {
                Log::warning('EcommerceSecurity: Bot blocked', [
                    'ip'         => $request->ip(),
                    'bot'        => $bot,
                    'user_agent' => $request->userAgent(),
                    'url'        => $request->fullUrl(),
                ]);
                abort(403, 'Access Denied');
            }
        }

        // ── 2. XSS في الـ GET Parameters ──────────────────
        foreach ($request->query() as $key => $value) {
            if (is_string($value)) {
                foreach (self::SUSPICIOUS_PATTERNS as $pattern) {
                    if (preg_match($pattern, $value)) {
                        Log::warning('EcommerceSecurity: Suspicious GET param', [
                            'ip'      => $request->ip(),
                            'param'   => $key,
                            'value'   => substr($value, 0, 100),
                            'url'     => $request->fullUrl(),
                        ]);
                        abort(400, 'Bad Request');
                    }
                }
            }
        }

        // ── 3. Suspicious Headers ──────────────────────────
        $suspiciousHeaders = ['X-Forwarded-Host', 'X-Host'];
        foreach ($suspiciousHeaders as $header) {
            if ($request->hasHeader($header)) {
                $headerValue = $request->header($header);
                if ($headerValue !== $request->getHost()) {
                    Log::warning('EcommerceSecurity: Suspicious header', [
                        'ip'     => $request->ip(),
                        'header' => $header,
                        'value'  => $headerValue,
                    ]);
                }
            }
        }

        // ── 4. Request Size Limit (DOS Prevention) ─────────
        $contentLength = (int) $request->header('Content-Length', 0);
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($contentLength > $maxSize) {
            abort(413, 'Payload Too Large');
        }

        return $next($request);
    }
}

// ker
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    protected $routeMiddleware = [
        // Other middleware
        'reception' => \App\Http\Middleware\reception::class,

        'engineer' => \App\Http\Middleware\engineer::class,
    ];


    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'ecommerce.security' => \App\Http\Middleware\EcommerceSecurityMiddleware::class,
    ];
}
// bootsapp
<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'ecommerce.security' => \App\Http\Middleware\EcommerceSecurityMiddleware::class,
        // أضف هنا
    ]);
})

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
// porsty
/* ── Page Header ── */
.page-header {
    background: var(--stroke);
    padding-block: 1.75rem;
    border-bottom: 1px solid var(--stroke);
    margin-bottom: 0;
    min-height: 200px;
    display: flex;
    justify-content: center;
    align-items: center;
}
.page-header__title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--heading);
    margin-bottom: 0.4rem;
}
.breadcrumb-item a {
    color: var(--primary);
}
.breadcrumb-item.active {
    color: var(--text);
}

/* ── Filter Sidebar ── */
.filter-card {
    border: 1px solid var(--stroke);
    border-radius: var(--radius-md);
    overflow: hidden;
}

.filter-card__header {
    padding: 0.75rem 1rem;
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--heading);
    background: var(--bg-secondary);
    border: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    text-align: right;
    width: 100%;
    transition: background var(--transition);
}

.filter-card__header:hover {
    background: var(--stroke);
}

/* السهم بيتحول سموز */
.filter-chevron {
    transition: transform 0.25s ease;
}
.filter-card__body {
    padding: 0.75rem 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.filter-count {
    background: var(--bg-secondary);
    border-radius: 99px;
    font-size: 0.75rem;
    padding: 0.1rem 0.5rem;
    color: var(--text);
}
.price-range {
    width: 100%;
    accent-color: var(--primary);
}
/* Color Swatch المحدث */
.color-swatches {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
    padding: 0.25rem 0;
}

.color-swatch {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
    outline: 2px solid transparent;
    outline-offset: 2px;
    transition:
        transform 0.2s,
        outline-color 0.2s,
        border-color 0.2s;
    flex-shrink: 0;
}

.color-swatch:hover {
    transform: scale(1.15);
}

/* الـ active state — border بيظهر لما يتاختار */
.color-swatch.is-active {
    border-color: var(--white);
    outline-color: var(--primary);
}

/* الـ disabled checkboxes تبان شفافة شوية */
.form-check-input:disabled ~ .form-check-label {
    opacity: 0.5;
    cursor: not-allowed;
}

/* ── Toolbar ── */
.products-toolbar {
    background: var(--white);
    border-radius: var(--radius-md);
}

@media (max-width: 767px) {
    .products-toolbar {
        flex-direction: row-reverse;
    }
}
.toolbar-sort {
    font-size: 0.9rem;
    border-radius: 12px;
    border-color: var(--stroke);
    padding: 4px 8px;
}
.products-count {
    font-size: 0.9rem;
}
.view-toggle {
    display: flex;
    gap: 0.25rem;
}
.view-btn {
    background: none;
    border: 1px solid var(--stroke);
    border-radius: var(--radius-sm);
    padding: 0.3rem 0.5rem;
    color: var(--text);
    cursor: pointer;
    transition:
        background var(--transition),
        color var(--transition);
}
.view-btn.active,
.view-btn:hover {
    background: var(--primary);
    color: var(--white);
    border-color: var(--primary);
}

.products-section .products-grid {
    grid-template-columns: repeat(3, 1fr);
}

@media (max-width: 767px) {
    .products-section .products-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* ── List View ── */
.products-grid--list {
    grid-template-columns: 1fr !important;
}

.products-grid--list .product-card {
    flex-direction: row;
    align-items: center;
    width: 100%;
    max-width: 100% !important;
    min-width: 0; /* مهم عشان الـ flex item ميتمددش برا */
    max-height: 200px !important;
}

.products-grid--list .product-card__img-wrap {
    max-width: 275px;
    min-width: 120px;
    flex-shrink: 0;
    aspect-ratio: 1;
    height: 168px;
}

.products-grid--list .product-card__body {
    flex: 1;
    min-width: 0; /* مهم جداً — بيمنع overflow في الـ flex */
    padding-inline-start: 0.75rem;
}

.products-grid--list .product-card__name {
    -webkit-line-clamp: 2;
    font-size: 0.95rem;
}

.products-grid--single {
    grid-template-columns: 1fr !important;
}

.products-grid--single .product-card {
    flex-direction: row;
    align-items: center;
    width: 100%;
    max-width: 100% !important;
    min-width: 0;
    max-height: 200px !important;
}

.products-grid--single .product-card__img-wrap {
    width: 275px;
    min-width: 100px;
    height: 168px;
    flex-shrink: 0;
    aspect-ratio: 1;
    background-color: var(--bg-secondary);
}
.products-grid--single .product-card__img-wrap img {
    height: 122px;
}

.products-grid--single .product-card__body {
    flex: 1;
    min-width: 0;
    padding-inline-start: 0.75rem;
}

/* ── Mobile: الكارت يملي العرض كامل دايماً ──
   في الموبايل تحت 576 الكروت دايماً full width
   سواء كانت في grid أو list
*/
@media (max-width: 575px) {
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .products-grid .product-card {
        width: 100%;
    }

    .products-grid--single,
    .products-grid--list {
        grid-template-columns: 1fr !important;
    }

    /* .products-grid--single .product-card__img-wrap,
    .products-grid--list .product-card__img-wrap {
        width: 120px;
        min-width: 120px;
    } */
}

.products-pagination {
    justify-content: flex-start !important;
}

/* Tablet — بدون sidebar */
@media (min-width: 768px) and (max-width: 991px) {
    #filtersSidebar {
        display: none !important;
    }
    .col-lg-9 {
        width: 100% !important;
        max-width: 100% !important;
    }
    .products-grid:not(.products-grid--list) {
        grid-template-columns: repeat(3, 1fr);
    }
}

.offcanvas {
    z-index: 2000 !important;
}

.filter-btn button,
.filter-btn button:active {
    border: none !important;
}

@media (min-width: 768px) {
    .filter-btn {
        display: none !important;
    }
}

.search-indicator__wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--bg-secondary);
    border: 1px solid var(--stroke);
    border-radius: var(--radius-md);
    padding: 0.625rem 1rem;
    gap: 1rem;
    flex-wrap: wrap;
}

.search-indicator__text {
    font-size: 0.9rem;
    color: var(--text);
}

.search-indicator__text strong {
    color: var(--heading);
}

.search-indicator__count {
    color: var(--text);
    opacity: 0.7;
    font-size: 0.85rem;
}

.search-indicator__clear {
    font-size: 0.85rem;
    color: var(--red);
    text-decoration: none;
    display: flex;
    align-items: center;
    white-space: nowrap;
    transition: opacity 0.2s;
}

.search-indicator__clear:hover {
    opacity: 0.7;
}
/* ── About Cards ── */
.about-card {
    background: var(--white);
    border: 1px solid var(--stroke);
    border-radius: var(--radius-lg);
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    transition: box-shadow var(--transition);
}

.about-card:hover {
    box-shadow: var(--shadow-md);
}

.about-card__icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    background: var(--primary);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
}

.about-card__title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--heading);
    margin: 0;
}

.about-card__text {
    font-size: 0.9rem;
    color: var(--text);
    line-height: 1.8;
    margin: 0;
}

/* ── Team Cards ── */
.team-card {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    height: 382px;
    background-color: var(--bg-secondary);
    border-top-left-radius: var(--radius-lg);
    border-top-right-radius: var(--radius-lg);
}

.team-card__img-wrap {
    width: 100%;
    height: 320px;
    overflow: hidden;
    margin-bottom: 0.25rem;
    padding: var(--radius-md);
}

.team-card__img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-top-left-radius: var(--radius-lg);
    border-top-right-radius: var(--radius-lg);
}

/* Placeholder لو مفيش صورة */
.team-card__img-wrap--placeholder {
    background: var(--bg-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: var(--text);
}

.team-card__name {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--heading);
    margin: 0;
}

.team-card__role {
    font-size: 0.78rem;
    color: var(--text);
    margin: 0;
}

/* ── About Branch Footer ── */
.about-branch {
    border-top: 1px solid var(--stroke);
    padding-top: 2rem;
}

.about-branch__inner {
    display: flex;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.about-branch__logo {
    max-height: 48px;
    width: auto;
    object-fit: contain;
}

.about-branch__info {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.about-branch__info p {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.88rem;
    color: var(--text);
    margin: 0;
}

.about-branch__info i {
    color: var(--primary);
}

@media (max-width: 575px) {
    .about-branch__inner {
        flex-direction: column;
        align-items: flex-start;
    }
}

/* ══════════════════════
   Contact Form
══════════════════════ */
.contact-map-wrap {
    border-radius: var(--radius-md);
    overflow: hidden;
    border: 1px solid var(--stroke);
}

.contact-form-card,
.contact-info-card {
    background: var(--white);
    border: 1px solid var(--stroke);
    border-radius: var(--radius-lg);
    padding: 1.75rem;
    height: 100%;
}

.contact-form-card__title,
.contact-info-card__title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--heading);
    margin-bottom: 1rem;
}

.contact-form-card__field {
    margin-bottom: 1rem;
}

.contact-form-card .form-label {
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--heading);
    margin-bottom: 0.35rem;
    display: block;
    text-align: right;
}

.contact-form-card .form-control {
    border-radius: var(--radius-sm);
    border-color: var(--stroke);
    font-size: 0.9rem;
    resize: none;
    direction: rtl !important;
}

.contact-form-card .form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 64, 102, 172), 0.12);
}

.contact-info-card__desc {
    font-size: 0.88rem;
    color: var(--text);
    line-height: 1.7;
    margin-bottom: 1.25rem;
}

.contact-info-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.contact-info-list__item {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    font-size: 0.88rem;
    color: var(--text);
    flex-direction: row-reverse;
    justify-content: flex-end;
}
#contactForm button[type="submit"] {
    background-color: var(--stroke);
    color: var(--text);
    font-size: 0.88rem;
    font-weight: bold;
}

#contactForm button[type="submit"]:hover {
    background-color: var(--heading);
    color: var(--white);
}

.contact-info-list__item i {
    color: var(--text);
    margin-top: 2px;
}
.contact-info-list__item a {
    color: var(--text);
    text-decoration: none;
}
.contact-info-list__item a:hover {
    color: var(--primary);
}

/* ══════════════════════
   Cart
══════════════════════ */
.cart-empty {
    text-align: center;
    padding: 4rem 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.cart-empty__icon {
    font-size: 4rem;
    color: var(--red);
    opacity: 0.7;
}

.cart-empty__title {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--heading);
    margin: 0;
}

.cart-empty__desc {
    font-size: 0.9rem;
    color: var(--text);
    max-width: 420px;
    line-height: 1.7;
    margin: 0;
}

.cart-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.cart-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--white);
    border: 1px solid var(--stroke);
    border-radius: var(--radius-md);
    padding: 1rem;
    flex-wrap: wrap;
}

.cart-item__img-wrap {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--stroke);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-item__img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.cart-item__info {
    flex: 1;
    min-width: 0;
}

.cart-item__name {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--heading);
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.cart-item__name:hover {
    color: var(--primary);
}

.cart-item__prices {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
}
.cart-item__price {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--heading);
}
.cart-item__old-price {
    font-size: 0.8rem;
    color: var(--text);
    text-decoration: line-through;
}
.cart-item__discount-label {
    font-size: 0.75rem;
    color: var(--red);
}

.cart-item__actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
}

.cart-qty {
    display: flex;
    align-items: center;
    border: 1px solid var(--stroke);
    border-radius: var(--radius-sm);
    overflow: hidden;
}

.cart-qty__btn {
    background: var(--bg-secondary);
    border: none;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition:
        background 0.2s,
        color 0.2s;
}

.cart-qty__btn:hover {
    background: var(--primary);
    color: var(--white);
}

.cart-qty__val {
    padding: 0 0.75rem;
    font-weight: 600;
    font-size: 0.9rem;
    min-width: 32px;
    text-align: center;
}

.cart-item__meta {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.cart-item__action-btn {
    background: none;
    border: none;
    font-size: 0.82rem;
    color: var(--text);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0;
    transition: color 0.2s;
}

.cart-item__action-btn:hover {
    color: var(--red);
}

.cart-item__total {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--heading);
}

.cart-summary {
    background: var(--white);
    border: 1px solid var(--stroke);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    position: sticky;
    top: 80px;
}

.cart-summary__title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--heading);
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--stroke);
}

.cart-summary__row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    font-size: 0.88rem;
    color: var(--text);
    border-bottom: 1px solid var(--stroke);
}

.cart-summary__row--total {
    font-weight: 700;
    font-size: 1rem;
    color: var(--heading);
    border-bottom: none;
    margin-top: 0.5rem;
}

.cart-summary__note {
    font-size: 0.78rem;
    color: var(--text);
    opacity: 0.7;
    margin-top: 0.5rem;
}

.cart-nav-btn {
    width: 36px;
    height: 36px;
    border: 1px solid var(--stroke);
    border-radius: 50%;
    background: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition:
        background 0.2s,
        color 0.2s;
}

.cart-nav-btn:hover {
    background: var(--primary);
    color: var(--white);
    border-color: var(--primary);
}

/* ══════════════════════
   Checkout
══════════════════════ */
.checkout-summary {
    background: var(--white);
    border: 1px solid var(--stroke);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    position: sticky;
    top: 80px;
}

.checkout-summary__title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--heading);
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--stroke);
}

.checkout-summary__item {
    padding: 1rem 0;
    border-bottom: 1px solid var(--stroke);
}

.checkout-summary__item-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    flex-direction: row-reverse;
}

.checkout-summary__item-info img {
    border-radius: var(--radius-sm);
    border: 1px solid var(--stroke);
    object-fit: contain;
}

.checkout-summary__item-name {
    font-weight: 600;
    font-size: 0.88rem;
    color: var(--heading);
    margin: 0;
}

.checkout-summary__item-sub {
    font-size: 0.78rem;
    margin: 0;
}

.checkout-summary__item-prices {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    font-size: 0.85rem;
    color: var(--text);
}

.checkout-summary__total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    font-weight: 700;
    font-size: 1rem;
    color: var(--heading);
}

.checkout-steps {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.checkout-step {
    border: 1px solid var(--stroke);
    border-radius: var(--radius-md);
    overflow: hidden;
}

.checkout-step--disabled {
    opacity: 0.5;
    pointer-events: none;
}

.checkout-step__header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    background: var(--bg-secondary);
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.checkout-step__num {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--primary);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.checkout-step__title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--heading);
    margin: 0;
}

.checkout-step__body {
    padding: 1.25rem;
}

.checkout-login-prompt {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

/* ══════════════════════
   Error 404
══════════════════════ */
.error-page {
    min-height: 60vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 1rem;
    padding: 3rem 1rem;
}

.error-page__code {
    font-size: clamp(5rem, 15vw, 10rem);
    font-weight: 900;
    color: var(--stroke);
    line-height: 1;
}

.error-page__msg {
    font-size: 1rem;
    color: var(--text);
    margin: 0;
}

/* ══════════════════════
   Policy
══════════════════════ */
.policy-content {
    max-width: 800px;
}

.policy-section {
    margin-bottom: 2rem;
}

.policy-section h2 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--heading);
    margin-bottom: 0.75rem;
    text-align: right;
}

.policy-section p,
.policy-section ol {
    font-size: 0.9rem;
    color: var(--text);
    line-height: 1.8;
    text-align: right;
    direction: rtl;
}

.policy-section ol {
    padding-right: 1.25rem;
    padding-left: 0;
}
.policy-section ol li {
    margin-bottom: 0.4rem;
}

/* ══════════════════════
   Error 404
══════════════════════ */
.error-page {
    min-height: 60vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 1rem;
    padding: 3rem 1rem;
}

.error-page__code {
    font-size: clamp(5rem, 15vw, 10rem);
    font-weight: 900;
    color: var(--stroke);
    line-height: 1;
}

.error-page__msg {
    font-size: 1rem;
    color: var(--text);
    margin: 0;
}

/* أضف في products.css */

/* ══════════════════════
   Auth Pages
══════════════════════ */

.auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    background: var(--bg-secondary);
}

.auth-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 2.5rem 2rem;
    width: 100%;
    max-width: 420px;
    box-shadow: var(--shadow-md);
    display: flex;
    flex-direction: column;
    gap: 1rem;
    text-align: center;
}

.auth-card__logo img {
    max-height: 48px;
    width: auto;
    object-fit: contain;
}

.auth-card__title {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--heading);
    margin: 0;
}

.auth-card__desc {
    font-size: 0.88rem;
    color: var(--text);
    margin: 0;
    line-height: 1.6;
}

.auth-card__phone {
    font-weight: 700;
    color: var(--primary);
    font-size: 1rem;
    margin: 0;
    direction: ltr;
}

.auth-card__errors {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: var(--radius-sm);
    padding: 0.75rem 1rem;
    text-align: right;
}

.auth-card__errors p {
    font-size: 0.85rem;
    color: var(--red);
    margin: 0;
}

.auth-card__field {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    text-align: right;
}

.auth-card__label {
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--heading);
}

.auth-card__input {
    padding: 0.65rem 0.875rem;
    border: 1px solid var(--stroke);
    border-radius: var(--radius-sm);
    font-size: 0.95rem;
    color: var(--heading);
    font-family: inherit;
    direction: rtl;
    text-align: right;
    width: 100%;
    transition:
        border-color 0.2s,
        box-shadow 0.2s;
}

.auth-card__input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(64, 102, 172, 0.12);
}

.auth-card__input.is-invalid {
    border-color: var(--red);
}

.auth-card__error-msg {
    font-size: 0.78rem;
    color: var(--red);
}

.auth-card__submit {
    width: 100%;
    padding: 0.75rem;
    font-size: 0.95rem;
    border-radius: var(--radius-sm);
}

.auth-card__footer,
.auth-card__terms {
    font-size: 0.82rem;
    color: var(--text);
    margin: 0;
}

.auth-card__link {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
}

.auth-card__link:hover {
    text-decoration: underline;
}

.auth-card__resend {
    font-size: 0.82rem;
    color: var(--text);
    margin: 0;
}

/* ── OTP Inputs ── */
.otp-inputs {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    direction: ltr; /* الأرقام من اليسار لليمين */
}

.otp-input {
    width: 48px;
    height: 56px;
    text-align: center;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--heading);
    border: 2px solid var(--stroke);
    border-radius: var(--radius-sm);
    transition:
        border-color 0.2s,
        box-shadow 0.2s;
    caret-color: var(--primary);
}

.otp-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(64, 102, 172, 0.12);
}

/* لما فيه قيمة */
.otp-input:not(:placeholder-shown) {
    border-color: var(--primary);
    background: rgba(64, 102, 172, 0.04);
}

/* لو كان في error */
.otp-input.is-invalid {
    border-color: var(--red);
}

/* زرار المتابعة لما يكتمل */
.auth-card__submit.is-ready {
    background: var(--primary);
    opacity: 1;
}

/* ── Welcome Page ── */
.auth-welcome__icon {
    font-size: 3.5rem;
    color: #16a34a;
}

/* ── Delete Confirm Modal ── */
#deleteConfirmModal .modal-content {
    border-radius: var(--radius-lg);
    border: none;
    box-shadow: var(--shadow-md);
}

/* ── Wishlist Active State ── */
.product-card__wish-btn.is-wishlisted i,
.pd-wish-btn.is-wishlisted i,
.js-save-later.is-wishlisted i {
    color: var(--red);
}

.product-card__wish-btn.is-wishlisted {
    border-color: var(--red);
}

@media (max-width: 420px) {
    .auth-card {
        padding: 2rem 1.25rem;
    }
    .otp-input {
        width: 40px;
        height: 48px;
        font-size: 1.1rem;
    }
}

// js
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
// resources/js/app.js
import 'bootstrap';
// أو
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;  // ← المهم — بيخليه متاح globally
// nav
@props([
    'staticLinks' => [],
    'navData' => [],
    'branchName' => '',
    'branchImage' => '',
    'phone' => '',
    'logo' => '',
    'ecommerceSharedData' => [],
])
@php
    $logo = $ecommerceSharedData['logo'] ?? $logo;

@endphp

{{-- ══════════════════════════════════════════
     TOP BAR
══════════════════════════════════════════ --}}
<div class="top-bar py-1 bg-var-primary text-white d-none d-md-block" aria-label="الشريط العلوي">
    <div class="container container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center top-bar__wrapper">
            {{-- اسم الفرع --}}
            <span class="top-bar__branch d-flex align-items-center gap-2">
                English
            </span>
            {{-- رقم التليفون --}}
            <a href="tel:{{ $ecommerceSharedData['phone'] }}"
                class="top-bar__phone text-white text-decoration-none d-flex align-items-center gap-2 flex-row-reverse"
                aria-label="اتصل بنا على {{ $ecommerceSharedData['phone'] }}">
                <i class="bi bi-telephone-fill" aria-hidden="true"></i>
                <span>{{ $ecommerceSharedData['phone'] }}</span>
            </a>


        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MAIN NAVBAR
══════════════════════════════════════════ --}}
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top w-100" role="navigation"
    aria-label="القائمة الرئيسية">

    <div class="container container-fluid px-4">


        {{-- ── زرار الـ Hamburger للموبايل ── --}}
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false" aria-label="فتح القائمة">
            <span class="navbar-toggler-icon"></span>
        </button>
        {{-- ── الجزء الأول: اللوجو ── --}}
        <a class="navbar-brand" href="{{ route('home') }}" aria-label="الصفحة الرئيسية">
            <img src="{{ asset($ecommerceSharedData['logo']) }}" alt="شعار المتجر" width="120" height="40"
                loading="eager">
        </a>
        {{-- Search --}}
        <div class="d-flex justify-content-between align-items-center gap-3 d-lg-none">
            <button type="button" class="btn btn-link p-0 text-dark" id="navSearchBtn" aria-label="بحث"
                aria-haspopup="dialog" aria-expanded="false">
                <i class="bi bi-search fs-5" aria-hidden="true"></i>
            </button>
            <a href="{{ route('ShoppingCart') }}" class="btn btn-link p-0 text-dark position-relative"
                aria-label="سلة التسوق">
                <i class="bi bi-bag fs-5" aria-hidden="true"></i>
                {{-- Badge عدد المنتجات --}}
                <span
                    class="cart-badge position-absolute top-0 start-100 translate-middle
                            badge rounded-pill bg-var-heading"
                    aria-label="عدد المنتجات في السلة">
                    {{ session('cart_count', 0) }}
                </span>
            </a>
        </div>


        {{-- ── الجزء التاني: اللينكات (flex-1) ── --}}
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto gap-1">

                @foreach ($staticLinks as $link)
                    @php
                        $children = $navData[$link['db_key']] ?? [];
                        $hasChildren = !empty($link['has_db']) && !empty($link['db_key']) && count($children) > 0;
                        $isProducts = $link['db_key'] === 'products';
                    @endphp

                    <li class="nav-item {{ $hasChildren ? 'dropdown' : '' }}">

                        @if ($hasChildren)
                            <a class="nav-link dropdown-toggle fw-medium" href="#" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" aria-expanded="false">
                                {{ $link['name'] }}
                            </a>

                            <ul class="dropdown-menu border-0 shadow-sm">
                                <li>
                                    <a class="dropdown-item fw-semibold text-primary border-bottom pb-2 mb-1"
                                        href="{{ route($link['route']) }}">
                                        <i class="bi bi-arrow-right ms-1"></i>
                                        عرض الكل
                                    </a>
                                </li>

                                @if ($isProducts)
                                    {{--
                                        dropdown المنتجات — بيعرض المنتجات مباشرة
                                        كل منتج ليه لينك على ProductDetails
                                    --}}
                                    @foreach ($children as $product)
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('ProductDetails', $product['slug']) }}">
                                                {{ $product['name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    {{--
                                        dropdown التصنيفات — بيعرض categories
                                        لو category عندها subcategories → dropend
                                        لو لأ → link مباشر
                                    --}}
                                    @foreach ($children as $cat)
                                        @if (count($cat['children']) > 0)
                                            <li class="dropend">
                                                <a class="dropdown-item dropdown-toggle"
                                                    href="{{ route('CategoryProduct', $cat['slug']) }}"
                                                    data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                                    aria-expanded="false">
                                                    {{ $cat['name'] }}
                                                </a>
                                                <ul class="dropdown-menu border-0 shadow-sm">
                                                    @foreach ($cat['children'] as $sub)
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('SubcategoryProduct', $sub['slug']) }}">
                                                                {{ $sub['name'] }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @else
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('CategoryProduct', $cat['slug']) }}">
                                                    {{ $cat['name'] }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                @endif

                            </ul>
                        @else
                            <a class="nav-link fw-medium" href="{{ route($link['route']) }}"
                                @if (request()->routeIs($link['route'])) aria-current="page" @endif>
                                {{ $link['name'] }}
                            </a>
                        @endif

                    </li>
                @endforeach

            </ul>

            {{-- ── الجزء التالت: الأيقونات ── --}}
            <div class="navbar-icons d-flex align-items-center gap-3 ms-lg-3 mt-3 mt-lg-0">
                {{-- Search --}}
                <button type="button" class="d-none d-md-flex btn btn-link p-0 text-dark" id="navSearchBtnDesk"
                    aria-label="بحث" aria-haspopup="dialog" aria-expanded="false">
                    <i class="bi bi-search fs-5" aria-hidden="true"></i>
                </button>


                {{-- User --}}
                @if (session('customer_name') === null)
                    <a href="{{ route('CustomerLogin') }}" class="btn btn-link p-0 text-dark" aria-label="حسابي">
                        <i class="bi bi-person fs-5" aria-hidden="true"></i>
                    </a>
                @else
                    <a href="{{ route('UserPersonalPage') }}" class="btn btn-link p-0 text-dark" aria-label="حسابي">
                        <i class="bi bi-person-check-fill" aria-hidden="true"></i>
                    </a>
                @endif

                {{-- Heart - i like this --}}

                {{-- في components/navbar.blade.php --}}

                {{-- السلة --}}
                <a href="{{ route('ShoppingCart') }}"
                class="btn-link position-relative"
                aria-label="سلة التسوق">
                    <i class="bi bi-bag fs-5" aria-hidden="true"></i>
                    @php $cartCount = count(session('cart', [])); @endphp
                    <span class="cart-badge {{ $cartCount === 0 ? 'd-none' : '' }}"
                        data-cart-count
                        aria-live="polite"
                        aria-label="{{ $cartCount }} منتج في السلة">
                        {{ $cartCount }}
                    </span>
                </a>

                {{-- المفضلة --}}
                <a href="{{ route('Wishlist') }}"
                class="btn-link position-relative"
                aria-label="قائمة الرغبات">
                    <i class="bi bi-heart fs-5" aria-hidden="true"></i>
                    @php $wishlistCount = count(session('wishlist', [])); @endphp
                    <span class="cart-badge {{ $wishlistCount === 0 ? 'd-none' : '' }}"
                        data-wishlist-count
                        aria-live="polite"
                        aria-label="{{ $wishlistCount }} في المفضلة">
                        {{ $wishlistCount }}
                    </span>
                </a>

            </div>
        </div>
    </div>
</nav>
// prc
@props([
    'id'         => 0,
    'name'       => '',
    'price'      => 0,
    'offerPrice' => null,
    'image'      => '',
    'route'      => 'ProductDetails',
    'hasOffer'   => false,
])

@php
    $showOffer    = $hasOffer
                 && $offerPrice !== null
                 && (float)$offerPrice > 0
                 && (float)$offerPrice < (float)$price;
    $discount     = $showOffer
                 ? round((((float)$price - (float)$offerPrice) / (float)$price) * 100)
                 : 0;
    $displayPrice = $showOffer ? $offerPrice : $price;

    // تحقق هل المنتج في المفضلة
    $inWishlist = in_array($id, session('wishlist', []));
@endphp

<article class="product-card">

    @if($showOffer && $discount > 0)
    <span class="product-card__badge" aria-label="خصم {{ $discount }}%">
        {{ $discount }}%
    </span>
    @endif

    <a href="{{ route($route, $id) }}"
       class="product-card__img-wrap"
       aria-label="عرض {{ $name }}">
        @if($image)
        <img src="{{ asset('images/productsimages/' . $image) }}"
             alt="{{ $name }}"
             class="product-card__img"
             width="148" height="120"
             loading="lazy"
             decoding="async">
        @else
        <div class="product-card__no-img" aria-hidden="true">
            <i class="bi bi-image"></i>
        </div>
        @endif
    </a>

    <div class="product-card__body">
        <h3 class="product-card__name">
            <a href="{{ route($route, $id) }}">{{ $name }}</a>
        </h3>

        <div class="product-card__prices">
            <span class="product-card__price {{ $showOffer ? 'product-card__price--offer' : '' }}">
                {{ number_format($displayPrice) }} ج.م
            </span>
            @if($showOffer)
            <span class="product-card__old-price">
                {{ number_format($price) }}
            </span>
            @endif
        </div>

        <div class="product-card__actions">
            {{--
                js-add-to-cart: AJAX إضافة للسلة
                btn-text: span منفصل عشان نغيره بـ DOM API (مش innerHTML)
            --}}
            <button type="button"
                    class="btn product-card__add-btn js-add-to-cart"
                    data-id="{{ $id }}"
                    aria-label="أضف {{ $name }} إلى السلة">
                <i class="bi bi-bag" aria-hidden="true"></i>
                <span class="btn-text">أضف إلى السلة</span>
            </button>

            {{--
                js-wishlist-toggle: AJAX toggle المفضلة
                is-wishlisted: class بتضاف لو في المفضلة
            --}}
            <button type="button"
                    class="btn product-card__wish-btn js-wishlist-toggle {{ $inWishlist ? 'is-wishlisted' : '' }}"
                    data-id="{{ $id }}"
                    aria-label="{{ $inWishlist ? 'إزالة من المفضلة' : 'أضف للمفضلة' }}"
                    aria-pressed="{{ $inWishlist ? 'true' : 'false' }}">
                <i class="bi bi-heart{{ $inWishlist ? '-fill' : '' }}" aria-hidden="true"></i>
            </button>
        </div>
    </div>

</article>
// chekk{{-- resources/views/ecommerce/Checkout/Checkout.blade.php --}}
@php
    $cartItems = session('cart', []);
    $cartTotal = collect($cartItems)->sum(function($item) {
        $price = ($item['offer_price'] ?? 0) > 0 && $item['offer_price'] < $item['price']
            ? $item['offer_price'] : $item['price'];
        return $price * ($item['quantity'] ?? 1);
    });
@endphp

@extends('layouts.app')
@section('title', 'إتمام الطلب — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')

<x-page-header title="إتمام الطلب" :breadcrumbs="[
    ['name' => 'الرئيسية', 'url' => route('home')],
    ['name' => 'سلة التسوق', 'url' => route('ShoppingCart')],
    ['name' => 'إتمام الطلب', 'url' => route('checkout')],
]" />

<div class="container py-5">
    <div class="row g-4 flex-row-reverse">

        {{-- ── ملخص الطلب (يمين) ── --}}
        <div class="col-lg-5">
            <div class="checkout-summary">
                <h3 class="checkout-summary__title">ملخص الطلب</h3>

                @foreach($cartItems as $i => $item)
                @php
                    $itemPrice = ($item['offer_price'] ?? 0) > 0 && $item['offer_price'] < $item['price']
                        ? $item['offer_price'] : $item['price'];
                @endphp
                <div class="checkout-summary__item">
                    <div class="checkout-summary__item-info">
                        <img src="{{ asset('images/productsimages/' . ($item['image'] ?? 'placeholder.png')) }}"
                             alt="{{ $item['name'] ?? '' }}"
                             width="48" height="48"
                             loading="lazy">
                        <div>
                            <p class="checkout-summary__item-name">
                                طلب {{ $i + 1 }}
                            </p>
                            <p class="checkout-summary__item-sub text-muted">
                                {{ Str::limit($item['name'] ?? '', 30) }}
                            </p>
                        </div>
                    </div>
                    <div class="checkout-summary__item-prices">
                        <div class="d-flex justify-content-between">
                            <span>كمية</span>
                            <span>{{ $item['quantity'] ?? 1 }} منتج</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>سعر المنتج</span>
                            <span>{{ number_format($itemPrice) }} جنية</span>
                        </div>
                        @if(($item['offer_price'] ?? 0) > 0 && $item['offer_price'] < $item['price'])
                        <div class="d-flex justify-content-between text-danger">
                            <span>الخصم</span>
                            <span>{{ number_format($item['price'] - $item['offer_price']) }} جنية</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between fw-bold">
                            <span>إجمالي السعر</span>
                            <span>{{ number_format($itemPrice * ($item['quantity'] ?? 1)) }} جنية</span>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="checkout-summary__total">
                    <span>إجمالي الطلب</span>
                    <span>{{ number_format($cartTotal) }} جنية</span>
                </div>

                @if(session('customer_phone'))
                <form method="POST" action="{{ route('checkout.confirm') }}" id="checkoutForm">
                    @csrf
                    <button type="submit" class="btn hero__btn w-100 mt-3">
                        تأكيد الطلب
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- ── البيانات الأساسية (يسار) ── --}}
        <div class="col-lg-7">
            <div class="checkout-steps">

                {{-- Step 1: البيانات الأساسية --}}
                <div class="checkout-step">
                    <div class="checkout-step__header">
                        <span class="checkout-step__num">1</span>
                        <h3 class="checkout-step__title">البيانات الأساسية</h3>
                    </div>

                    <div class="checkout-step__body">
                        @if(!session('customer_phone'))
                        <div class="checkout-login-prompt">
                            <div class="d-flex align-items-start gap-2 mb-3 p-3"
                                 style="background:var(--bg-secondary);border-radius:var(--radius-md)">
                                <i class="bi bi-info-circle text-primary mt-1" aria-hidden="true"></i>
                                <p class="mb-0 text-muted" style="font-size:.88rem">
                                    وجود حساب خاص بك ضروري لمتابعة تأكيد الطلب. وبمقدورك من ثم اتباع طلباتك أونلاين.
                                </p>
                            </div>
                            <a href="{{ route('CustomerLogin') }}"
                               class="btn hero__btn w-100">
                                تسجيل الدخول
                            </a>
                            <p class="text-center mt-2" style="font-size:.85rem">
                                مستخدم جديد؟
                                <a href="{{ route('NewCustomer') }}" class="text-primary">إنشاء حساب</a>
                            </p>
                        </div>
                        @else
                        <div class="p-3"
                             style="background:var(--bg-secondary);border-radius:var(--radius-md)">
                            <p class="mb-0">
                                <i class="bi bi-person-check-fill text-success me-2" aria-hidden="true"></i>
                                مرحباً، <strong>{{ session('customer_name') }}</strong>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Step 2: تفاصيل التسليم --}}
                <div class="checkout-step checkout-step--disabled">
                    <div class="checkout-step__header">
                        <span class="checkout-step__num">2</span>
                        <h3 class="checkout-step__title">تفاصيل التسليم</h3>
                    </div>
                </div>

                {{-- Step 3: تفاصيل السداد --}}
                <div class="checkout-step checkout-step--disabled">
                    <div class="checkout-step__header">
                        <span class="checkout-step__num">3</span>
                        <h3 class="checkout-step__title">تفاصيل السداد</h3>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
// custlog
{{-- resources/views/ecommerce/Customer/CustomerCode/CustomerCode.blade.php --}}
@extends('layouts.app')
@section('title', 'تأكيد رقم الموبايل — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')
<div class="auth-page">
    <div class="auth-card">

        <h1 class="auth-card__title">
            {{-- لو CustomerCode → "تأكيد رقم الموبايل" --}}
            {{-- لو CustomerLogin → "الدخول بكود رسالة قصيرة" --}}
            تأكيد رقم الموبايل
        </h1>
        <p class="auth-card__desc">أدخل الكود من الرسالة المرسلة على رقم</p>
        <p class="auth-card__phone">{{ session('_pending_phone') ?? '' }}</p>

        @if($errors->any())
        <div class="auth-card__errors" role="alert">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST"
              action="{{ route('CustomerCodePost') }}"
              id="codeForm"
              novalidate>
            @csrf
            @method('post')

            {{-- البيانات المشفرة --}}
            <input type="hidden" name="customer_name"           value="{{ $encryptedcustomer_name ?? '' }}">
            <input type="hidden" name="customer_telegramchatid" value="{{ $encryptedcustomer_telegramchatid ?? '' }}">
            <input type="hidden" name="customer_email"          value="{{ $encryptedcustomer_email ?? '' }}">
            <input type="hidden" name="customer_phone"          value="{{ $encryptedcustomer_phone ?? '' }}">
            <input type="hidden" name="customer_systemcode"     value="{{ $encryptednewCustomerCode ?? '' }}">

            {{-- OTP Inputs — 6 خانات --}}
            <div class="otp-inputs" role="group" aria-label="كود التحقق">
                @for($i = 0; $i < 6; $i++)
                <input type="text"
                       class="otp-input"
                       maxlength="1"
                       inputmode="numeric"
                       pattern="[0-9]"
                       aria-label="الرقم {{ $i + 1 }}"
                       autocomplete="one-time-code">
                @endfor
                {{-- input مخفي بيجمع الكود الكامل --}}
                <input type="hidden" name="customer_code" id="otpFinal">
            </div>

            <p class="auth-card__resend">
                <span id="resendTimer">إعادة إرسال الكود خلال <strong id="countdown">30</strong> ثانية</span>
                <button type="button"
                        id="resendBtn"
                        class="auth-card__link d-none"
                        aria-label="إعادة إرسال الكود">
                    إعادة الإرسال
                </button>
            </p>

            <button type="submit" class="btn hero__btn auth-card__submit" id="continueBtn" disabled>
                متابعة
            </button>
        </form>

        <p class="auth-card__terms">
            بالمتابعة، فأنت موافق على
            <a href="{{ route('TermsAndConditions') }}" class="auth-card__link">الشروط والأحكام</a>
        </p>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /*
     | OTP Logic:
     | 1. 6 inputs — كل input يستقبل رقم واحد
     | 2. لما يكتب رقم بينتقل للـ input الجاي تلقائياً
     | 3. لما يحذف بيرجع للـ input السابق
     | 4. لما يكمل الـ 6 أرقام بيفعّل زرار المتابعة
    */

    const inputs      = document.querySelectorAll('.otp-input');
    const finalInput  = document.getElementById('otpFinal');
    const continueBtn = document.getElementById('continueBtn');
    const form        = document.getElementById('codeForm');

    inputs.forEach((input, idx) => {
        input.addEventListener('input', function () {
            // السماح بالأرقام فقط
            this.value = this.value.replace(/\D/g, '').slice(-1);

            if (this.value && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }

            _syncOTP();
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && idx > 0) {
                inputs[idx - 1].focus();
                inputs[idx - 1].value = '';
                _syncOTP();
            }
        });

        // Paste support — لو نسخ الكود كامل
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData)
                .getData('text')
                .replace(/\D/g, '')
                .slice(0, 6);

            pasted.split('').forEach((char, i) => {
                if (inputs[i]) inputs[i].value = char;
            });

            if (inputs[pasted.length - 1]) {
                inputs[pasted.length - 1].focus();
            }

            _syncOTP();
        });
    });

    function _syncOTP() {
        const code = Array.from(inputs).map(i => i.value).join('');
        finalInput.value = code;

        // تفعيل زرار المتابعة لما يكتمل الكود
        if (code.length === 6) {
            continueBtn.disabled = false;
            continueBtn.classList.add('is-ready');
        } else {
            continueBtn.disabled = true;
            continueBtn.classList.remove('is-ready');
        }
    }

    // Focus على أول input
    if (inputs[0]) inputs[0].focus();

    /*
     | Countdown Timer — إعادة الإرسال
    */
    let timeLeft = 30;
    const countdownEl = document.getElementById('countdown');
    const resendTimer = document.getElementById('resendTimer');
    const resendBtn   = document.getElementById('resendBtn');

    const timer = setInterval(() => {
        timeLeft--;
        if (countdownEl) countdownEl.textContent = timeLeft;

        if (timeLeft <= 0) {
            clearInterval(timer);
            if (resendTimer) resendTimer.classList.add('d-none');
            if (resendBtn)   resendBtn.classList.remove('d-none');
        }
    }, 1000);

    // إعادة الإرسال
    if (resendBtn) {
        resendBtn.addEventListener('click', function () {
            // ← هنا ممكن تضيف AJAX لإعادة إرسال الكود
            // دلوقتي بنعمل reload عادي
            window.location.reload();
        });
    }

})();
</script>
@endpush
// cust 
// 
{{-- resources/views/ecommerce/Customer/CustomerLogin/CustomerLogin.blade.php --}}
@extends('layouts.app')
@section('title', 'تسجيل الدخول — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')
<div class="auth-page">
    <div class="auth-card">

        {{-- Logo --}}
        <div class="auth-card__logo">
            <img src="{{ asset('images/brancheslogo/' . ($ecommerceSharedData['branchImage'] ?? '')) }}"
                 alt="{{ $ecommerceSharedData['branchName'] ?? '' }}"
                 height="48" loading="eager">
        </div>

        <h1 class="auth-card__title">أهلا بك!</h1>
        <p class="auth-card__desc">أستخدم رقم موبايلك لتسجيل الدخول أو إنشاء حساب.</p>

        {{-- Errors --}}
        @if($errors->any())
        <div class="auth-card__errors" role="alert">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        {{-- Form --}}
        <form method="POST"
              action="{{ route('CustomerLoginPost') }}"
              id="loginForm"
              novalidate>
            @csrf
            @method('post')
            <input type="hidden" name="fingerprint" id="fingerprint">
            <input type="hidden" name="client_token" id="client_token">

            <div class="auth-card__field">
                <label for="customer_phone" class="auth-card__label">رقم الموبايل</label>
                <input type="tel"
                       id="customer_phone"
                       name="customer_phone"
                       class="auth-card__input @error('customer_phone') is-invalid @enderror"
                       placeholder="{{ $ecommerceSharedData['phone'] ?? '01xxxxxxxxx' }}"
                       pattern="01[0-9]{9}"
                       maxlength="11"
                       required
                       autocomplete="tel"
                       inputmode="numeric"
                       aria-describedby="phoneHelp">
                @error('customer_phone')
                <span class="auth-card__error-msg" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn hero__btn auth-card__submit">
                الدخول بكود رسالة قصيرة
            </button>
        </form>

        <p class="auth-card__footer">
            مستخدم جديد؟
            <a href="{{ route('CustomerLogin') }}" class="auth-card__link">إنشاء حساب</a>
        </p>

        <p class="auth-card__terms">
            بالمتابعة، فأنت موافق على
            <a href="{{ route('TermsAndConditions') }}" class="auth-card__link">الشروط والأحكام</a>
        </p>

    </div>
</div>
@endsection
// cust
{{-- resources/views/ecommerce/Customer/CustomerWelcome/CustomerWelcome.blade.php --}}
@extends('layouts.app')
@section('title', 'مرحباً — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')
<div class="auth-page">
    <div class="auth-card text-center">

        <div class="auth-welcome__icon" aria-hidden="true">
            <i class="bi bi-check-circle-fill"></i>
        </div>

        <h1 class="auth-card__title">تهانينا!</h1>
        <p class="auth-card__desc">تم إنشاء حسابك بنجاح</p>

        <a href="{{ route('ShoppingCart') }}"
           class="btn hero__btn auth-card__submit mt-3">
            إستكمال عملية الشراء
        </a>

    </div>
</div>

@push('scripts')
<script>
    // Redirect تلقائي بعد 3 ثواني للـ home
    setTimeout(() => {
        // لو المستخدم مش ضغط على الزرار — نوديه للهوم
        // يمكن تعديل الـ route حسب احتياجك
    }, 3000);
</script>
@endpush

@endsection
// newcas
{{-- resources/views/ecommerce/Customer/NewCustomer/NewCustomer.blade.php --}}
@extends('layouts.app')
@section('title', 'إنشاء حساب — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')
<div class="auth-page">
    <div class="auth-card">

        <div class="auth-card__logo">
            <img src="{{ asset('images/brancheslogo/' . ($ecommerceSharedData['branchImage'] ?? '')) }}"
                 alt="{{ $ecommerceSharedData['branchName'] ?? '' }}"
                 height="48" loading="eager">
        </div>

        <h1 class="auth-card__title">إنشاء حساب</h1>
        <p class="auth-card__desc">أستخدم رقم موبايلك لتسجيل الدخول أو إنشاء حساب.</p>

        @if($errors->any())
        <div class="auth-card__errors" role="alert">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST"
              action="{{ route('NewCustomerPost') }}"
              novalidate>
            @csrf
            @method('post')
            <input type="hidden" name="fingerprint" id="fingerprint">
            <input type="hidden" name="client_token" id="client_token">
            {{-- رقم التليفون مشفر من الخطوة السابقة --}}
            <input type="hidden"
                   name="customer_phone"
                   value="{{ $encryptedcustomer_phone ?? '' }}">

            <div class="auth-card__field">
                <label for="customer_name" class="auth-card__label">الاسم ثلاثي</label>
                <input type="text"
                       id="customer_name"
                       name="customer_name"
                       class="auth-card__input @error('customer_name') is-invalid @enderror"
                       required
                       autocomplete="name"
                       aria-label="الاسم الكامل">
                @error('customer_name')
                <span class="auth-card__error-msg" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="auth-card__field">
                <label for="customer_telegramchatid" class="auth-card__label">رقم الموبايل</label>
                <input type="tel"
                       id="customer_telegramchatid"
                       name="customer_telegramchatid"
                       class="auth-card__input"
                       placeholder="اختياري — رقم شات التليجرام"
                       inputmode="numeric">
            </div>

            <button type="submit" class="btn hero__btn auth-card__submit">
                إنشاء حساب
            </button>
        </form>

        <p class="auth-card__footer">
            عندك حساب؟
            <a href="{{ route('CustomerLogin') }}" class="auth-card__link">تسجيل الدخول</a>
        </p>

        <p class="auth-card__terms">
            بالمتابعة، فأنت موافق على
            <a href="{{ route('TermsAndConditions') }}" class="auth-card__link">الشروط والأحكام</a>
        </p>

    </div>
</div>
@endsection
// prodt
 @php
     $product = $Product?->product;
     $sellPrice = (float) ($product?->product_sellprice ?? 0);
     $offerPrice = (float) ($product?->product_offerprice ?? 0);
     $hasOffer = $offerPrice > 0 && $offerPrice < $sellPrice;
     $discount = $hasOffer ? round((($sellPrice - $offerPrice) / $sellPrice) * 100) : 0;
     $stock = (int) ($product?->{'232021'} ?? 0);
     $mainImage = $product?->product_image ?? null;
     $extraImages = array_filter([
         $product?->product_image2 ?? null,
         $product?->product_image3 ?? null,
         $product?->product_image4 ?? null,
     ]);
     if (empty($extraImages) && $mainImage) {
         $extraImages = [$mainImage, $mainImage, $mainImage];
     }
     $allThumbs = $mainImage ? array_values(array_merge([$mainImage], array_values($extraImages))) : [];
     $hasRealReviews = ($reviewsCount ?? 0) > 0;
     $avgDisplay = $hasRealReviews ? round($avgRating ?? 0) : 4; /* | Specs من الـ
 description | لو كل سطر فيه ":" بيتحول لجدول | لو مفيش — بيعرض placeholder */
     $specs = [];
     if ($product?->product_description) {
         foreach (explode("\n", $product->product_description) as $line) {
             $parts = explode(':', trim($line), 2);
             if (count($parts) === 2 && trim($parts[0]) && trim($parts[1])) {
                 $specs[] = ['key' => trim($parts[0]), 'val' => trim($parts[1])];
             }
         }
     }
     $placeholderSpecs = [
         ['key' => 'الضمان', 'val' => 'سنة واحدة'],
         ['key' => 'اسم الموديل', 'val' => $product?->product_name ?? '—'],
         ['key' => 'ماركة', 'val' => '—'],
         ['key' => 'اللون', 'val' => '—'],
         ['key' => 'سعة التخزين', 'val' => '—'],
         ['key' => 'رام', 'val' => '—'],
         ['key' => 'الكاميرا الخلفية', 'val' => '—'],
         [
             'key' => 'الكاميرا
الأمامية',
             'val' => '—',
         ],
     ];
     $displaySpecs = !empty($specs) ? $specs : $placeholderSpecs;
     $placeholderReviews = [
         [
             'name' => 'Mohamed ali',
             'rating' => 5,
             'date' => '08 نوفمبر 2024',
             'comment' => 'موبايل رائع و مريح و بطاريته
ممتازة',
         ],
         ['name' => 'Ahmed maged', 'rating' => 5, 'date' => '08 نوفمبر 2024', 'comment' => 'مريح جدا و عملي جدا'],
     ];
 @endphp @extends('layouts.app')
 @section('title', ($product?->product_name ?? 'تفاصيل المنتج') . ' — ' . ($ecommerceSharedData['branchName'] ?? ''))
 @section('description', $product?->product_description ? Str::limit(strip_tags($product->product_description), 160) :
     '') @if ($mainImage)
         @section('og_image', asset('images/productsimages/' . $mainImage))
     @endif
     @section('content')
         <div class="container py-4">
             {{-- ══════════════════════════════════════════════ TOP SECTION
    ══════════════════════════════════════════════ --}}
             <div class="pd-top">

                 {{-- العمود 1: الصور الصغيرة --}}
                 <div class="pd-col-thumbs">
                     @foreach ($allThumbs as $i => $img)
                         <button class="pd-thumb {{ $i === 0 ? 'is-active' : '' }}" type="button"
                             aria-label="صورة {{ $i + 1 }}"
                             onclick="switchImg(this, '{{ asset('images/productsimages/' . $img) }}')">
                             <img src="{{ asset('images/productsimages/' . $img) }}" alt="{{ $product?->product_name ?? '' }}"
                                 width="80" height="120" loading="{{ $i === 0 ? 'eager' : 'lazy' }}" />
                         </button>
                     @endforeach
                 </div>
                 {{-- العمود 2: الصورة الكبيرة --}}
                 <div class="pd-col-main-img">
                     <div class="pd-main-img-wrap">
                         @if ($hasOffer)
                             <span class="pd-offer-badge">{{ $discount }}%</span>
                         @endif
                         <img id="mainImg"
                             src="{{ $mainImage ? asset('images/productsimages/' . $mainImage) : asset('images/placeholder.png') }}"
                             alt="{{ $product?->product_name ?? '' }}" class="pd-main-img" width="500" height="500"
                             loading="eager" fetchpriority="high" />
                     </div>

                     {{-- الصور الصغيرة في الموبايل — تحت الصورة الكبيرة --}}
                     {{-- <div class="pd-thumbs-mobile d-flex d-lg-none mt-2">
                         @foreach ($allThumbs as $i => $img)
                             <button class="pd-thumb {{ $i === 0 ? 'is-active' : '' }}" type="button"
                                 onclick="switchImg(this, '{{ asset('images/productsimages/' . $img) }}')">
                                 <img src="{{ asset('images/productsimages/' . $img) }}"
                                     alt="{{ $product?->product_name ?? '' }}" width="80" height="120" loading="lazy" />
                             </button>
                         @endforeach
                     </div> --}}
                 </div>

                 {{-- العمود 3: Info --}}
                 <div class="pd-col-info">
                     {{-- الاسم --}}
                     <h1 class="pd-title">{{ $product?->product_name ?? '' }}</h1>

                     {{-- الوصف --}}
                     @if ($product?->product_description && empty($specs))
                         <p class="pd-short-desc">
                             {{ Str::limit($product->product_description, 200) }}
                         </p>
                     @endif {{-- السعر --}}
                     <div class="pd-price-block">
                         @if ($hasOffer)
                             <span class="pd-price pd-price--offer">{{ number_format($offerPrice) }} جنية</span>
                             <span class="pd-price pd-price--old">{{ number_format($sellPrice) }}</span>
                             <span class="pd-discount-chip">خصم {{ $discount }}%</span>
                         @else
                             <span class="pd-price">{{ number_format($sellPrice) }} جنية</span>
                         @endif
                     </div>

                     {{-- Stock --}}
                     <div class="pd-stock-row">
                         المنتج: @if ($stock > 0)
                             <span class="pd-stock-chip pd-stock-chip--in">
                                 <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                                 متوفر
                             </span>
                             (عدد {{ $stock }})
                         @else
                             <span class="pd-stock-chip pd-stock-chip--out">
                                 <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
                                 غير متوفر
                             </span>
                         @endif
                     </div>

                     {{-- Actions --}}
                     <!-- <div class="pd-actions">
                         {{-- القلب --}}
                         <button class="btn product-card__wish-btn" type="button" aria-label="المفضلة">
                             <i class="bi bi-heart"></i>
                         </button>

                         {{-- زرار السلة --}}
                         @if (session('customer_phone'))
                             <form method="POST"
                                 action="{{ route('CustomerRequestIncreseQuantityPost', $Product?->ecommerceproduct_id) }}"
                                 class="pd-cart-form">
                                 @csrf
                                 <input type="hidden" name="quantity" id="hiddenQty" value="1" />
                                 <button type="submit" class="btn hero__btn pd-cart-btn">
                                     <i class="bi bi-basket" aria-hidden="true"></i>
                                     أضف الى السلة
                                 </button>
                             </form>
                         @else
                             <a href="{{ route('CustomerLogin') }}" class="btn hero__btn pd-cart-btn">
                                 <i class="bi bi-basket" aria-hidden="true"></i>
                                 أضف الى السلة
                             </a>
                         @endif {{-- Qty --}}
                         <div class="pd-qty">
                             <button type="button" class="pd-qty-btn" id="qtyPlus">
                                 <i class="bi bi-plus" aria-hidden="true"></i>
                             </button>
                             <output id="qtyInput" class="pd-qty-val">1</output>
                             <button type="button" class="pd-qty-btn" id="qtyMinus">
                                 <i class="bi bi-dash" aria-hidden="true"></i>
                             </button>
                         </div>
                     </div> -->

                    {{-- في ProductDetails.blade.php -- قسم الـ Actions --}}

                    {{-- Actions --}}
                    <div class="pd-actions">

                        {{-- القلب — Wishlist Toggle --}}
                        @php $inWishlist = in_array($Product?->ecommerceproduct_id, session('wishlist', [])); @endphp
                        <button type="button"
                                class="btn pd-wish-btn js-wishlist-toggle {{ $inWishlist ? 'is-wishlisted' : '' }}"
                                data-id="{{ $Product?->ecommerceproduct_id }}"
                                aria-label="{{ $inWishlist ? 'إزالة من المفضلة' : 'أضف للمفضلة' }}"
                                aria-pressed="{{ $inWishlist ? 'true' : 'false' }}">
                            <i class="bi bi-heart{{ $inWishlist ? '-fill' : '' }}" aria-hidden="true"></i>
                        </button>

                        {{-- زرار السلة — Add to Cart AJAX --}}
                        <button type="button"
                                class="btn hero__btn pd-cart-btn js-add-to-cart"
                                data-id="{{ $Product?->ecommerceproduct_id }}"
                                aria-label="أضف {{ $product?->product_name ?? '' }} إلى السلة">
                            <i class="bi bi-basket" aria-hidden="true"></i>
                            <span class="btn-text">أضف الى السلة</span>
                        </button>

                        {{-- Qty --}}
                        <div class="pd-qty" role="group" aria-label="الكمية">
                            <button type="button" class="pd-qty-btn" id="qtyPlus" aria-label="زيادة">
                                <i class="bi bi-plus" aria-hidden="true"></i>
                            </button>
                            <output id="qtyInput" class="pd-qty-val" aria-live="polite">1</output>
                            <button type="button" class="pd-qty-btn" id="qtyMinus" aria-label="تقليل">
                                <i class="bi bi-dash" aria-hidden="true"></i>
                            </button>
                        </div>

                    </div>

                     {{-- Payment --}}
                     <div class="pd-payment">
                         <img src="{{ asset('/images/socialmediacontacts/backingmobile.webp') }}" alt="وسائل الدفع"
                             loading="lazy" width="280" height="60" />
                     </div>

                     {{-- Related Products --}} @if (isset($RelatedProducts) && $RelatedProducts->count())
                         <div class="pd-related">
                             <h2 class="pd-related-title">منتجات ممكن تحتاجها</h2>
                             <div class="pd-related-list">
                                 @foreach ($RelatedProducts as $rel)
                                     <a href="{{ route('ProductDetails', $rel->ecommerceproduct_id) }}"
                                         class="pd-related-card">
                                         <span class="pd-related-img__wrapper">
                                             <img src="{{ asset('images/productsimages/' . ($rel->product?->product_image ?? 'placeholder.png')) }}"
                                                 alt="{{ $rel->product?->product_name ?? '' }}" width="80" height="120"
                                                 loading="lazy" />
                                         </span>
                                         <span class="pd-related-card__name">
                                             {{ Str::limit($rel->product?->product_name ?? '', 18) }}
                                         </span>
                                         <span class="pd-related-card__price">
                                             <i class="bi bi-basket-fill" aria-hidden="true"></i>
                                             {{ number_format($rel->product?->product_sellprice ?? 0) }}
                                             ج.م
                                         </span>
                                     </a>
                                 @endforeach
                             </div>
                         </div>
                     @endif
                 </div>

                 {{-- العمود 4: التقييم — ديسكتوب فقط --}}
                 <div class="pd-col-rating d-none d-lg-flex">
                     <div class="pd-rating-col">
                         @for ($i = 1; $i <= 5; $i++)
                             <i class="bi bi-star-fill {{ $i <= $avgDisplay ? 'star-on' : 'star-off' }}"
                                 aria-hidden="true"></i>
                             @endfor @if ($hasRealReviews)
                                 <span class="pd-rating-count">(الآراء {{ $reviewsCount }})</span>
                             @else
                                 <span class="pd-rating-count pd-rating-count--muted">(الآراء)</span>
                             @endif
                     </div>
                 </div>
             </div>

             {{-- ══════════════════════════════════ SPECS + REVIEWS
    ══════════════════════════════════ --}}
             <div class="pd-section mt-4">
                 {{-- Specs --}}
                 <div class="pd-acc">
                     <button class="pd-acc__btn" type="button" data-bs-toggle="collapse" data-bs-target="#specsBody"
                         aria-expanded="true">
                         <span>مواصفات المنتج</span>
                         <i class="bi bi-chevron-up pd-acc__chevron" aria-hidden="true"></i>
                     </button>
                     <div class="collapse show" id="specsBody">
                         <div class="pd-acc__body p-0">
                             <table class="pd-specs-tbl">
                                 <tbody>
                                     @foreach ($displaySpecs as $spec)
                                         <tr>
                                             <td class="pd-specs-tbl__label">
                                                 {{ $spec['key'] }}
                                             </td>
                                             <td class="pd-specs-tbl__val">
                                                 {{ $spec['val'] }}
                                             </td>
                                         </tr>
                                     @endforeach
                                 </tbody>
                             </table>
                         </div>
                     </div>
                 </div>

                 {{-- Reviews --}}
                 <div class="pd-acc mt-3">
                     <button class="pd-acc__btn" type="button" data-bs-toggle="collapse" data-bs-target="#reviewsBody"
                         aria-expanded="true">
                         <span>
                             الأراء @if ($hasRealReviews)
                                 <span class="pd-acc__count">{{ $reviewsCount }}</span>
                             @endif
                         </span>
                         <i class="bi bi-chevron-up pd-acc__chevron" aria-hidden="true"></i>
                     </button>
                     <div class="collapse show" id="reviewsBody">
                         <div class="pd-acc__body">
                             {{-- Summary @if ($hasRealReviews) --}}
                             <div class="pd-reviews-summary mb-4">
                                 <small class="text-muted">تقييمات و آراء العملاء</small>
                                 <div class="d-flex justify-content-center align-items-center">
                                     <div class="pd-reviews-summary__score">
                                         {{ number_format($avgRating ?? 0, 1) }}
                                     </div>
                                     <div class="pd-review-stars mb-1">
                                         @for ($i = 1; $i <= 5; $i++)
                                             <i class="bi bi-star-fill {{ $i <= round($avgRating ?? 0) ? 'star-on' : 'star-off' }}"
                                                 aria-hidden="true"></i>
                                         @endfor
                                     </div>
                                 </div>
                             </div>
                             {{-- @endif  --}}
                             {{-- Reviews List --}}
                             @if ($hasRealReviews)
                                 @foreach ($Reviews as $review)
                                     <article class="pd-review">
                                         <div class="pd-review__header">
                                             <div class="pd-review__right">
                                                 <span class="text-muted" style="font-size: 0.78rem">بواسطة</span>
                                                 <span class="pd-review__author">
                                                     {{ $review->customer?->customer_name ?? 'مجهول' }}
                                                 </span>
                                             </div>
                                             <div class="pd-review__left">
                                                 <time class="pd-review__date"
                                                     datetime="{{ $review->created_at?->toISOString() }}">
                                                     {{ $review->created_at?->format('d نوفمبر
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               Y') }}
                                                 </time>
                                                 <div class="pd-review-stars">
                                                     @php$r = $review->customerproductcomment_rating;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           @endphp ?> ?> ?> ?> ?> ?> ?> ?> ?> ?> @for ($i = 1; $i <= 5; $i++)
                                                         <i class="bi bi-star-fill {{ $i <= $r ? 'star-on' : 'star-off' }}"
                                                             aria-hidden="true"></i>
                                                     @endfor
                                                     <span
                                                         style="
                                            font-size: 0.75rem;
                                            color: var(--text);
                                        ">{{ $r }}/5</span>
                                                 </div>
                                             </div>
                                         </div>
                                         <p class="pd-review__text">
                                             {{ $review->customerproductcomment_comment }}
                                         </p>
                                     </article>
                                 @endforeach
                             @else
                                 @foreach ($placeholderReviews as $pr)
                                     <article class="pd-review">
                                         <div class="pd-review__header">
                                             <div class="pd-review__right">
                                                 <span class="text-muted" style="font-size: 0.78rem">بواسطة</span>
                                                 <span class="pd-review__author">{{ $pr['name'] }}</span>
                                             </div>
                                             <div class="pd-review__left">
                                                 <time class="pd-review__date">{{ $pr['date'] }}</time>
                                                 <div class="pd-review-stars">
                                                     @for ($i = 1; $i <= 5; $i++)
                                                         <i class="bi bi-star-fill {{ $i <= $pr['rating'] ? 'star-on' : 'star-off' }}"
                                                             aria-hidden="true"></i>
                                                     @endfor
                                                     <span
                                                         style="
                                            font-size: 0.75rem;
                                            color: var(--text);
                                        ">{{ $pr['rating'] }}/5</span>
                                                 </div>
                                             </div>
                                         </div>
                                         <p class="pd-review__text">{{ $pr['comment'] }}</p>
                                     </article>
                                 @endforeach
                             @endif {{-- Write Review --}}
                             <div class="pd-write-review">
                                 <p class="pd-write-review__label">أكتب رأيك</p>

                                 {{-- النجوم --}}
                                 <div class="pd-star-input mb-3">
                                     @for ($i = 5; $i >= 1; $i--)
                                         <input type="radio" name="rating" id="star{{ $i }}"
                                             value="{{ $i }}" class="pd-star-input__radio" form="reviewForm" />
                                         <label for="star{{ $i }}" class="pd-star-input__label">
                                             <i class="bi bi-star-fill" aria-hidden="true"></i>
                                         </label>
                                     @endfor
                                 </div>

                                 {{-- الـ textarea دايماً ظاهر --}}
                                 <div class="pd-form-wrapper mb-3">
                                     <textarea name="comment" class="form-control" rows="1" placeholder="أكتب هنا" maxlength="500"
                                         form="reviewForm" aria-label="تعليقك"></textarea>

                                     @if (session('customer_phone'))
                                         <form method="POST" id="reviewForm"
                                             action="{{ route('CustomerAddProductCommentPost', $Product?->ecommerceproduct_id) }}">
                                             @csrf
                                             <button type="submit" class="btn hero__btn">
                                                 إرسال
                                             </button>
                                         </form>
                                     @else
                                         <a href="{{ route('CustomerLogin') }}" class="btn hero__btn w-100">
                                             إرسال
                                         </a>
                                     @endif
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

             {{-- Similar Products --}} @if (isset($SimilarProducts) && $SimilarProducts->count())
                 <div class="mt-5">
                     <div class="d-flex justify-content-between align-items-center mb-4">
                         <h2 class="section-title mb-0">منتجات بديلة</h2>
                         <a href="{{ route('EcommerceAllProducts') }}" class="section-more-link">
                             <i class="bi bi-arrow-right" aria-hidden="true"></i>
                             عرض المزيد
                         </a>
                     </div>
                     <div class="products-grid">
                         @foreach ($SimilarProducts as $ep)
                             <x-product-card :id="$ep->ecommerceproduct_id" :name="$ep->product?->product_name ?? ''" :price="$ep->product?->product_sellprice ?? 0" :offerPrice="$ep->product?->product_offerprice ?? null"
                                 :image="$ep->product?->product_image ?? ''" route="ProductDetails" :hasOffer="($ep->product?->product_offerprice ?? 0) > 0 &&
                                     $ep->product?->product_offerprice < $ep->product?->product_sellprice" />
                         @endforeach
                     </div>
                 </div>
             @endif
         </div>

         @endsection 
         @push('scripts')
        <script>
        (function() {
            'use strict';

            // ── صور المنتج ──
            function switchImg(btn, src) {
                const main = document.getElementById('mainImg');
                if (main) main.src = src;
                document.querySelectorAll('.pd-thumb').forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');
            }
            window.switchImg = switchImg;

            // ── Qty Control ──
            /*
            | qtyInput: الـ <output> اللي بيظهر الرقم
            | الـ js-add-to-cart في layouts/app.blade.php
            | بيقرأ منه قبل ما يبعت الـ request
            */
            const qtyDisplay = document.getElementById('qtyInput');
            let qty    = 1;
            const maxQty = {{ $stock > 0 ? min($stock, 10) : 10 }};

            function updateQty(val) {
                qty = Math.max(1, Math.min(val, maxQty));
                // textContent — آمن
                if (qtyDisplay) qtyDisplay.textContent = qty;
            }

            document.getElementById('qtyMinus')?.addEventListener('click', () => updateQty(qty - 1));
            document.getElementById('qtyPlus')?.addEventListener('click',  () => updateQty(qty + 1));

            // ── Collapse Chevron ──
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
                const target  = document.querySelector(btn.getAttribute('data-bs-target'));
                const chevron = btn.querySelector('.pd-acc__chevron');
                if (!target || !chevron) return;

                target.addEventListener('show.bs.collapse', () => {
                    chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
                });
                target.addEventListener('hide.bs.collapse', () => {
                    chevron.classList.replace('bi-chevron-up', 'bi-chevron-down');
                });
            });

        })();
        </script>
    @endpush
     
// shopcat
@php
    $Page_title = $ecommerceSharedData['pageTitle'] ?? '- سلة التسوق ';
    $description =
        'تسوق الآن من فرع ' . $ecommerceSharedData['branchName'] ?? ' واحصل على أفضل العروض والمنتجات المختارة بعناية.';
    '، عروض حصرية على المنتجات والخدمات لفترة محدودة.';
    $keywords = 'عروض, خصومات, ' . $ecommerceSharedData['branch']->branch_name . ', منتجات, خدمات, توفير, أسعار خاصة';
    $og_title = 'عروض فرع ' . $ecommerceSharedData['branch']->branch_name;
    $og_description =
        'استمتع بتجربة تسوق مميزة من فرع ' . $ecommerceSharedData['branch']->branch_name . ' – لا تفوت الفرصة!';
    $og_image = url('/images/brancheslogo/' . $ecommerceSharedData['branch']->branch_image);
    $og_type = 'website';
@endphp

@php
    /*
     | السلة محفوظة في الـ session عشان تشتغل حتى بدون تسجيل دخول
     | الـ session key: 'cart' → array من products
     |
     | لما يسجل دخول ويكمل الطلب هيتحول لـ CustomerRequestProduct
    */
    $cartItems = session('cart', []);
    $cartCount = count($cartItems);

    /*
     | حساب الإجمالي
     | كل item فيه: id, name, price, offer_price, quantity, image
    */
    $cartTotal = collect($cartItems)->sum(function ($item) {
        $price =
            ($item['offer_price'] ?? 0) > 0 && $item['offer_price'] < $item['price']
                ? $item['offer_price']
                : $item['price'];
        return $price * ($item['quantity'] ?? 1);
    });

    /*
     | المنتجات المشاهدة مؤخراً — من الـ session
     | ← لما يزور المستخدم ProductDetails بتتحفظ في session('recently_viewed')
    */
    $recentlyViewed = [];
    if (session('recently_viewed')) {
        $recentIds = session('recently_viewed', []);
        $recentlyViewed = \App\Models\EcommerceProduct::whereIn('ecommerceproduct_id', $recentIds)
            ->with('product')
            ->where('ecommerceproduct_displaystatus', 1)
            ->take(4)
            ->get();
    }
@endphp

@extends('layouts.app')


@section('title', $Page_title)
@section('description', $description)
@section('content')

    <x-page-header title="سلة التسوق" />

    <div class="container py-2">

        @if ($cartCount === 0)
            {{-- ══ السلة فارغة ══ --}}
            <div class="cart-empty">
                <div class="cart-empty__icon" aria-hidden="true">
                    <i class="bi bi-basket"></i>
                </div>
                <h2 class="cart-empty__title">سلة التسوق الخاصة بك فارغة!</h2>
                <p class="cart-empty__desc">
                    {{ $ecommerceSharedData['branchName'] ?? 'المتجر' }}  مكانك الأول لكل احتياجات الإلكترونية.
                    تسوق الأن مع أكبر تشكيلة منتجات </p>
                <a href="{{ route('EcommerceAllProducts') }}" class="btn hero__btn">
                    تسوق الآن
                </a>
            </div>
        @else
            {{-- ══ السلة فيها منتجات ══ --}}
            <div class="row g-4">

                {{-- ── قائمة المنتجات ── --}}
                <div class="col-lg-8">
                    <div class="cart-items">
                        @foreach ($cartItems as $key => $item)
                            <div class="cart-item" data-key="{{ $key }}">

                                {{-- صورة المنتج --}}
                                <a href="{{ route('ProductDetails', $item['id']) }}" class="cart-item__img-wrap">
                                    <img src="{{ asset('images/productsimages/' . ($item['image'] ?? 'placeholder.png')) }}"
                                        alt="{{ $item['name'] ?? '' }}" width="80" height="80" loading="lazy">
                                </a>

                                {{-- المعلومات --}}
                                <div class="cart-item__info">
                                    <a href="{{ route('ProductDetails', $item['id']) }}" class="cart-item__name">
                                        {{ $item['name'] ?? '' }}
                                    </a>

                                    @php
                                        $itemPrice =
                                            ($item['offer_price'] ?? 0) > 0 && $item['offer_price'] < $item['price']
                                                ? $item['offer_price']
                                                : $item['price'];
                                    @endphp

                                    <div class="cart-item__prices">
                                        <span class="cart-item__price">
                                            {{ number_format($itemPrice) }} جنية
                                        </span>
                                        @if (($item['offer_price'] ?? 0) > 0 && $item['offer_price'] < $item['price'])
                                            <span class="cart-item__old-price">
                                                {{ number_format($item['price']) }}
                                            </span>
                                            <span class="cart-item__discount-label">خصم</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Qty + Actions --}}
                                {{-- Qty + Actions --}}
                                <div class="cart-item__actions">

                                    {{-- ── Qty AJAX ── --}}
                                    <div class="cart-qty">
                                        {{--
                                            data-action: 'decrease' | 'increase'
                                            data-key: مفتاح المنتج في الـ session
                                            class js-qty-btn: الـ JS بيستمع عليها
                                        --}}
                                        <button type="button"
                                                class="cart-qty__btn js-qty-btn"
                                                data-action="decrease"
                                                data-key="{{ $key }}"
                                                aria-label="تقليل الكمية">
                                            <i class="bi bi-dash" aria-hidden="true"></i>
                                        </button>

                                        <span class="cart-qty__val">{{ $item['quantity'] ?? 1 }}</span>

                                        <button type="button"
                                                class="cart-qty__btn js-qty-btn"
                                                data-action="increase"
                                                data-key="{{ $key }}"
                                                aria-label="زيادة الكمية">
                                            <i class="bi bi-plus" aria-hidden="true"></i>
                                        </button>
                                    </div>

    <div class="cart-item__meta">

        {{--
            DELETE:
            ───────
            الـ form بياخد id فريد
            الـ button بياخد data-form-id يساوي نفس الـ id
            الـ JS بيجيب الـ form بـ getElementById
            ده الحل الصح لأن الـ button مش جوه الـ form في الـ DOM
        --}}
        @php $deleteFormId = 'delete-form-' . $key; @endphp

        <form method="POST"
              action="{{ route('cart.remove', $key) }}"
              id="{{ $deleteFormId }}"
              class="d-none">
            @csrf
            @method('DELETE')
        </form>

        <button type="button"
                class="cart-item__action-btn js-delete-confirm"
                data-form-id="{{ $deleteFormId }}"
                data-name="{{ e($item['name'] ?? '') }}"
                aria-label="حذف {{ $item['name'] ?? 'المنتج' }}">
            <i class="bi bi-trash" aria-hidden="true"></i>
            <span class="btn-text">حذف المنتج</span>
        </button>

        {{-- Save for Later --}}
        @php $inWishlist = in_array($key, session('wishlist', [])); @endphp
        <button type="button"
                class="cart-item__action-btn js-save-later {{ $inWishlist ? 'is-wishlisted' : '' }}"
                data-id="{{ $key }}"
                aria-label="{{ $inWishlist ? 'في المفضلة' : 'حفظ للمفضلة' }}"
                aria-pressed="{{ $inWishlist ? 'true' : 'false' }}">
            <i class="bi bi-heart{{ $inWishlist ? '-fill' : '' }}" aria-hidden="true"></i>
            <span class="btn-text">{{ $inWishlist ? 'في المفضلة' : 'حفظ لاحقاً' }}</span>
        </button>

    </div>

    {{-- الإجمالي — data-item-total لتحديثه بـ AJAX --}}
    <div class="cart-item__total">
        {{ number_format($itemPrice * ($item['quantity'] ?? 1)) }} جنية
    </div>

</div>

                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ── ملخص الطلب ── --}}
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h3 class="cart-summary__title">ملخص الطلب</h3>

                        <div class="cart-summary__row">
                            <span>عدد المنتجات</span>
                            <span>{{ $cartCount }}</span>
                        </div>

                        <div class="cart-summary__row">
                            <span>حالة</span>
                            <span class="text-success fw-semibold">جاهز</span>
                        </div>

                        <div class="cart-summary__row cart-summary__row--total">
                            <span>إجمالي الطلب</span>
                            {{-- data-cart-total: الـ JS بيحدثه بعد كل تغيير في الكمية --}}
                            <span data-cart-total>{{ number_format($cartTotal) }} جنية</span>
                        </div>

                        <div class="cart-summary__note">
                            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                            يتوقف على الشروط والأحكام
                        </div>

                        @if (session('customer_phone'))
                            <a href="{{ route('checkout') }}" class="btn hero__btn w-100 mt-3">
                                متابعة الشراء
                            </a>
                        @else
                            {{--
                    لو المستخدم مش مسجل دخول — بنوديه لـ checkout
                    والـ checkout بيطلب منه يسجل دخول
                --}}
                            <a href="{{ route('checkout') }}" class="btn hero__btn w-100 mt-3">
                                متابعة الشراء
                            </a>
                        @endif

                    </div>
                </div>

            </div>
        @endif

        {{-- ══ منتجات شاهدتها مؤخراً ══ --}}
        @if (count($recentlyViewed ?? []) > 0)
            <div class="mt-5">
                <h2 class="section-title mb-4">منتجات شاهدتها مؤخراً</h2>
                <div class="products-grid">
                    @foreach ($recentlyViewed as $ep)
                        <x-product-card :id="$ep->ecommerceproduct_id" :name="$ep->product?->product_name ?? ''" :price="$ep->product?->product_sellprice ?? 0" :offer-price="$ep->product?->product_offerprice"
                            :image="$ep->product?->product_image ?? ''" route="ProductDetails" :has-offer="($ep->product?->product_offerprice ?? 0) > 0 &&
                                $ep->product?->product_offerprice < $ep->product?->product_sellprice" />
                    @endforeach
                </div>

                {{-- Navigation arrows --}}
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <button class="cart-nav-btn" aria-label="السابق">
                        <i class="bi bi-chevron-right" aria-hidden="true"></i>
                    </button>
                    <button class="cart-nav-btn" aria-label="التالي">
                        <i class="bi bi-chevron-left" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        @endif

    </div>


    {{-- resources/views/ecommerce/ShoppingCart/ShoppingCart.blade.php --}}

    {{-- أضف الـ Modal قبل @endsection --}}

    {{-- ══ Delete Confirmation Modal — مرة واحدة بس ══ --}}
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1"
         aria-labelledby="deleteConfirmTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:380px">
            <div class="modal-content text-center p-4">

                <h5 class="fw-bold mb-2" id="deleteConfirmTitle">
                    هتحذف المنتج من مشترياتك؟
                </h5>
                <p class="text-muted mb-4" style="font-size:.88rem">
                    <span id="deleteProductName"></span>
                </p>

                <div class="d-flex gap-3 justify-content-center">
                    <button type="button"
                            id="confirmDeleteBtn"
                            class="btn hero__btn px-4">
                        احذف
                    </button>
                    <button type="button"
                            class="btn btn-outline-secondary px-4"
                            data-bs-dismiss="modal">
                        لا تحذف
                    </button>
                </div>

                <div class="mt-3">
                    <button type="button"
                            id="modalSaveToWishlist"
                            class="btn btn-link text-muted"
                            style="font-size:.82rem">
                        <i class="bi bi-heart me-1" aria-hidden="true"></i>
                        <span class="btn-text">اضافة للمفضلة</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection
// wshlis
@php
    /*
     | المفضلة في الـ session
     | session('wishlist') → array من product IDs
    */
    $wishlistIds = session('wishlist', []);
    $wishlistProducts = collect();
    if (!empty($wishlistIds)) {
        $wishlistProducts = \App\Models\EcommerceProduct::whereIn('ecommerceproduct_id', $wishlistIds)
            ->with('product')
            ->where('ecommerceproduct_displaystatus', 1)
            ->get();
    }
@endphp

@extends('layouts.app')
@section('title', 'قائمة الرغبات — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')

    <x-page-header title="قائمة الرغبات" />

    <div class="container">

        @if ($wishlistProducts->isEmpty())
            {{-- ══ المفضلة فارغة ══ --}}
            <div class="cart-empty">
                <div class="cart-empty__icon" aria-hidden="true">
                    <i class="bi bi-heart"></i>
                </div>
                <h2 class="cart-empty__title">قائمة الرغبات فارغة</h2>
                <p class="cart-empty__desc">
                    {{ $ecommerceSharedData['branchName'] }} مكانك الأول لكل احتياجات الإلكتورنية.
                    تسوق الأن مع أكبر تشكيلة منتجات
                </p>
                <a href="{{ route('EcommerceAllProducts') }}" class="btn hero__btn">
                    تسوق الآن
                </a>
            </div>
        @else
            {{-- ══ فيها منتجات ══ --}}
            <div class="products-grid">
                @foreach ($wishlistProducts as $ep)
                    <x-product-card :id="$ep->ecommerceproduct_id" :name="$ep->product?->product_name ?? ''" :price="$ep->product?->product_sellprice ?? 0" :offer-price="$ep->product?->product_offerprice"
                        :image="$ep->product?->product_image ?? ''" route="ProductDetails" :has-offer="($ep->product?->product_offerprice ?? 0) > 0 &&
                            $ep->product?->product_offerprice < $ep->product?->product_sellprice" />
                @endforeach
            </div>
        @endif

    </div>
@endsection
// ap
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', '')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- SEO --}}
    <title>@yield('title', 'Plus Digital')</title>
    <link rel="preload" href="{{ Vite::asset('resources/fonts/cairo/Cairo-Regular.woff2') }}" as="font"
        type="font/woff2" crossorigin>
    @stack('styles')
    @vite(['resources/css/app.css', 'resources/css/products.css', 'resources/css/page-details.css', 'resources/js/app.js'])
</head>

<body>
    @include('components.navbar', [
        'staticLinks' => $ecommerceSharedData['staticLinks'] ?? [],
        'navData' => $ecommerceSharedData['navData'] ?? [],
        'branchName' => $ecommerceSharedData['branchName'] ?? '',
        'branchImage' => $ecommerceSharedData['branchImage'] ?? '',
        'phone' => $ecommerceSharedData['phone'] ?? '',
        'logo' => $ecommerceSharedData['logo'] ?? '',
    ])

    <main id="main-content" role="main">
        @yield('content')
    </main>

    @include('components.footer', [
        'footerCategories' => $footerCategories ?? [],
        'branch' => $Branch ?? null,
        'SocialMediaContact' => $SocialMediaContact ?? null,
        'mapUrl' => $mapUrl ?? '#',
        'phone2' => $Branch?->branch_phone2 ?? '',
    ])


    @stack('scripts')

    {{-- ══════ Search Modal ══════ --}}
    <div id="searchModal" class="search-modal" role="dialog" aria-modal="true" aria-label="البحث عن منتج" hidden>

        {{-- Backdrop --}}
        <div class="search-modal__backdrop" id="searchBackdrop"></div>

        {{-- Panel --}}
        <div class="search-modal__panel">

            {{-- Input Row --}}
            <div class="search-modal__input-wrap">
                <button type="button" class="search-modal__close" id="searchModalClose" aria-label="إغلاق البحث">
                    <i class="bi bi-x-lg"></i>
                </button>

                <input type="search" id="searchModalInput" class="search-modal__input" placeholder="ابحث عن منتج..."
                    autocomplete="off" spellcheck="false" maxlength="100" dir="rtl">

                <i class="bi bi-search search-modal__icon" aria-hidden="true"></i>
            </div>

            {{-- Hint --}}
            <div class="search-modal__hint" id="searchHint">
                <i class="bi bi-search" aria-hidden="true"></i>
                <span>اكتب للبحث في المنتجات</span>
            </div>

            {{-- Loading --}}
            <div class="search-modal__loading d-none" id="searchLoading">
                <span class="search-spinner"></span>
                <span>جاري البحث...</span>
            </div>

            {{-- Quick Categories --}}
            @if (!empty($ecommerceSharedData['navData']['categories']))
                <div class="search-modal__quick">
                    <span class="search-modal__quick-label">تصفح بالتصنيف:</span>
                    <div class="search-modal__quick-tags">
                        @foreach (array_slice($ecommerceSharedData['navData']['categories'], 0, 6) as $cat)
                            <a href="{{ route('CategoryProduct', $cat['slug']) }}" class="search-modal__quick-tag">
                                {{ $cat['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
    <script>
        (function () {
            'use strict';

            // ── Helpers ──────────────────────────────────────
            function csrf() {
                return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            }

            function updateBadges(selector, count) {
                document.querySelectorAll(selector).forEach(el => {
                    // textContent آمن من XSS
                    el.textContent = count;
                    el.classList.toggle('d-none', Number(count) === 0);
                });
            }

            // ── Add to Cart ───────────────────────────────────
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.js-add-to-cart');
                if (!btn) return;
                e.preventDefault();

                const id = btn.dataset.id;
                if (!id) return;

                // قراءة الكمية من صفحة تفاصيل المنتج لو موجودة
                const qtyEl = document.getElementById('qtyInput');
                const qty   = qtyEl ? (parseInt(qtyEl.textContent, 10) || 1) : 1;

                const icon         = btn.querySelector('i');
                const text         = btn.querySelector('.btn-text');
                const originalIcon = icon?.className ?? 'bi bi-bag';
                const originalText = text?.textContent ?? 'أضف إلى السلة';

                btn.disabled = true;

                fetch(`/cart/add/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN':  csrf(),
                        'Accept':        'application/json',
                        'Content-Type':  'application/json',
                    },
                    // نبعت الكمية مع الـ request
                    body: JSON.stringify({ quantity: qty }),
                })
                .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(data => {
                    if (!data.success) { btn.disabled = false; return; }

                    if (icon) icon.className        = 'bi bi-check';
                    if (text) text.textContent      = 'تمت الإضافة';
                    updateBadges('[data-cart-count]', data.cart_count);

                    setTimeout(() => {
                        if (icon) icon.className   = originalIcon;
                        if (text) text.textContent = originalText;
                        btn.disabled = false;
                    }, 1500);
                })
                .catch(() => { btn.disabled = false; });
            });

            // ── Wishlist Toggle ───────────────────────────────
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.js-wishlist-toggle');
                if (!btn) return;
                e.preventDefault();

                const id = btn.dataset.id;
                if (!id) return;

                const icon = btn.querySelector('i');

                fetch(`/wishlist/toggle/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json', 'Content-Type': 'application/json' },
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    if (data.in_wishlist) {
                        if (icon) icon.className = 'bi bi-heart-fill';
                        btn.classList.add('is-wishlisted');
                        btn.setAttribute('aria-pressed', 'true');
                        btn.setAttribute('aria-label', 'إزالة من المفضلة');
                    } else {
                        if (icon) icon.className = 'bi bi-heart';
                        btn.classList.remove('is-wishlisted');
                        btn.setAttribute('aria-pressed', 'false');
                        btn.setAttribute('aria-label', 'أضف للمفضلة');
                    }
                    updateBadges('[data-wishlist-count]', data.wishlist_count);
                })
                .catch(console.error);
            });

            // ── Save for Later ────────────────────────────────
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.js-save-later');
                if (!btn) return;
                e.preventDefault();

                const id   = btn.dataset.id;
                const icon = btn.querySelector('i');
                const text = btn.querySelector('.btn-text');

                fetch(`/wishlist/toggle/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json', 'Content-Type': 'application/json' },
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    if (data.in_wishlist) {
                        if (icon) icon.className   = 'bi bi-heart-fill';
                        if (text) text.textContent = 'في المفضلة';
                        btn.classList.add('is-wishlisted');
                    } else {
                        if (icon) icon.className   = 'bi bi-heart';
                        if (text) text.textContent = 'حفظ لاحقاً';
                        btn.classList.remove('is-wishlisted');
                    }
                    updateBadges('[data-wishlist-count]', data.wishlist_count);
                })
                .catch(console.error);
            });

            // ── Cart Qty AJAX (زيادة/تقليل بدون refresh) ─────
            /*
            | الـ buttons بيحملوا:
            |   class="js-qty-btn"
            |   data-action="increase" أو "decrease"
            |   data-key="{{ '$key' }}"  ← مفتاح المنتج في الـ session
            */
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.js-qty-btn');
                if (!btn) return;
                e.preventDefault();

                const action  = btn.dataset.action;   // 'increase' | 'decrease'
                const itemKey = btn.dataset.key;
                if (!action || itemKey === undefined) return;

                // منع الضغط المتكرر
                if (btn.disabled) return;
                btn.disabled = true;

                const cartItem = btn.closest('.cart-item');
                const qtyEl    = cartItem?.querySelector('.cart-qty__val');
                const totalEl  = cartItem?.querySelector('.cart-item__total');
                const currentQty = parseInt(qtyEl?.textContent ?? '1', 10);

                fetch(`/cart/update/${itemKey}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN':  csrf(),
                        'Accept':        'application/json',
                        'Content-Type':  'application/json',
                    },
                    body: JSON.stringify({ action }),
                })
                .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(data => {
                    if (!data.success) { btn.disabled = false; return; }

                    if (data.removed) {
                        // المنتج اتحذف (الكمية وصلت لصفر)
                        cartItem?.remove();
                    } else {
                        // تحديث الكمية والإجمالي بدون Refresh
                        if (qtyEl)   qtyEl.textContent  = data.new_quantity;
                        if (totalEl) totalEl.textContent = data.item_total + ' جنية';
                    }

                    // تحديث الإجمالي الكلي
                    const summaryTotalEl = document.querySelector('[data-cart-total]');
                    if (summaryTotalEl) summaryTotalEl.textContent = data.cart_total + ' جنية';

                    updateBadges('[data-cart-count]', data.cart_count);

                    btn.disabled = false;
                })
                .catch(() => { btn.disabled = false; });
            });

            // ── Delete Confirm Modal ──────────────────────────
            /*
            | المشكلة كانت:
            | btn.closest('form') مش بيشتغل لأن الـ button
            | مش جوه الـ form — كل منهم في div منفصل
            |
            | الحل:
            | الـ form بياخد id="delete-form-{key}"
            | الـ button بياخد data-form-id="delete-form-{key}"
            | الـ JS بيجيب الـ form بـ getElementById
            */
            // ── Delete Confirm Modal ──────────────────────────────────
            let _pendingFormId = null;

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.js-delete-confirm');
                if (!btn) return;
                e.preventDefault();

                _pendingFormId = btn.dataset.formId;

                // textContent — آمن من XSS
                const nameEl = document.getElementById('deleteProductName');
                if (nameEl) nameEl.textContent = btn.dataset.name ?? 'هذا المنتج';

                const modalEl = document.getElementById('deleteConfirmModal');
                if (!modalEl) return;

                // ← الحل: نشيل aria-hidden قبل ما Bootstrap يضيفه غلط
                modalEl.removeAttribute('aria-hidden');

                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            });

            // تأكيد الحذف
            document.addEventListener('click', function (e) {
                if (!e.target.closest('#confirmDeleteBtn')) return;

                // ← هنا كانت المشكلة: modalEl معرفش هنا
                // الحل: نعرفه من جديد في نفس الـ scope
                const modalEl = document.getElementById('deleteConfirmModal');

                if (_pendingFormId) {
                    const form = document.getElementById(_pendingFormId);
                    if (form) {
                        // نخفي الـ modal الأول عشان ما يبقاش في conflict
                        if (modalEl && window.bootstrap) {
                            const bsModal = bootstrap.Modal.getInstance(modalEl);
                            if (bsModal) {
                                bsModal.hide();
                            }
                        }
                        // بعد ما الـ modal يتخفى نعمل submit
                        setTimeout(() => {
                            form.submit();
                        }, 200);
                    }
                    _pendingFormId = null;
                }
            });
            // ── Search Modal ──────────────────────────────────
            const PRODUCTS_URL = '{{ route("EcommerceAllProducts") }}';
            const searchModal  = document.getElementById('searchModal');
            const backdrop     = document.getElementById('searchBackdrop');
            const searchInput  = document.getElementById('searchModalInput');
            const closeBtn     = document.getElementById('searchModalClose');
            const trigger      = document.getElementById('navSearchBtn');
            const triggerDesk  = document.getElementById('navSearchBtnDesk');
            const hint         = document.getElementById('searchHint');
            const loading      = document.getElementById('searchLoading');

            if (searchModal && trigger && searchInput) {
                let isOpen   = false;
                let debounce = null;

                function openModal() {
                    searchModal.removeAttribute('hidden');
                    requestAnimationFrame(() => searchModal.classList.add('search-modal--open'));
                    isOpen = true;
                    trigger.setAttribute('aria-expanded', 'true');
                    if (triggerDesk) triggerDesk.setAttribute('aria-expanded', 'true');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => searchInput.focus(), 50);
                }

                function closeModal() {
                    searchModal.classList.remove('search-modal--open');
                    isOpen = false;
                    trigger.setAttribute('aria-expanded', 'false');
                    if (triggerDesk) triggerDesk.setAttribute('aria-expanded', 'false');
                    document.body.style.overflow = '';
                    setTimeout(() => {
                        searchModal.setAttribute('hidden', '');
                        searchInput.value = '';
                        if (hint)    hint.classList.remove('d-none');
                        if (loading) loading.classList.add('d-none');
                    }, 260);
                }

                function goToResults(query) {
                    if (!query || query.trim().length < 2) return;
                    if (hint)    hint.classList.add('d-none');
                    if (loading) loading.classList.remove('d-none');
                    const url = new URL(PRODUCTS_URL);
                    url.searchParams.set('search', query.trim());
                    window.location.href = url.toString();
                }

                trigger.addEventListener('click', openModal);
                if (triggerDesk) triggerDesk.addEventListener('click', openModal);
                if (backdrop)    backdrop.addEventListener('click', closeModal);
                if (closeBtn)    closeBtn.addEventListener('click', closeModal);

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape' && isOpen) closeModal();
                });

                searchInput.addEventListener('input', function () {
                    const val = this.value.trim();
                    clearTimeout(debounce);
                    if (val.length < 2) {
                        if (hint)    hint.classList.remove('d-none');
                        if (loading) loading.classList.add('d-none');
                        return;
                    }
                    debounce = setTimeout(() => goToResults(val), 3000);
                });

                searchInput.addEventListener('keydown', e => {
                    if (e.key === 'Enter') { clearTimeout(debounce); goToResults(searchInput.value); }
                });
            }

        })();
    </script>
    {{-- ══════ End Search Modal ══════ --}}
</body>

</html>
// rost
<?php

require __DIR__ . '/auth.php';

use App\Http\Controllers\Ecommerce\Checkout\CheckoutController;
use App\Http\Controllers\Ecommerce\Customer\CustomerCode\CustomerCodeController;
use App\Http\Controllers\Ecommerce\Customer\CustomerLogin\CustomerLoginController;
use App\Http\Controllers\Ecommerce\Customer\CustomerWelcome\CustomerWelcomeController;
use App\Http\Controllers\Ecommerce\Customer\NewCustomer\NewCustomerController;
use App\Http\Controllers\Ecommerce\EcommerceAllCategories\EcommerceAllCategoriesController;
use App\Http\Controllers\Ecommerce\EcommerceContactUs\EcommerceContactUsController;
use App\Http\Controllers\Ecommerce\EcommerceKnowAboutUs\EcommerceKnowAboutUsController;
use App\Http\Controllers\Ecommerce\EcommerceOffers\EcommerceOffersController;
use App\Http\Controllers\Ecommerce\EcommerceWelcomeController;
use App\Http\Controllers\Ecommerce\Maintenance\UserMaintenanceController;
use App\Http\Controllers\Ecommerce\PersonalPage\UserPersonalPageController;
use App\Http\Controllers\Ecommerce\PrivacyPolicy\PrivacyPolicyController;
use App\Http\Controllers\Ecommerce\ProductDetails\ProductDetailsController;
use App\Http\Controllers\Ecommerce\Products\CategoryProductController;
use App\Http\Controllers\Ecommerce\Products\CompanyProductController;
use App\Http\Controllers\Ecommerce\Products\EcommerceAllProductsController;
use App\Http\Controllers\Ecommerce\Products\EcommerceMostSaleProductsController;
use App\Http\Controllers\Ecommerce\Products\EcommerceProductsWithOffersController;
use App\Http\Controllers\Ecommerce\Products\SubcategoryProductController;
use App\Http\Controllers\Ecommerce\ShoppingCart\ShoppingCartController;
use App\Http\Controllers\Ecommerce\Wishlist\WishlistController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════
// الـ Group الرئيسي مع الـ Security Middleware
// ══════════════════════════════════════════════
Route::prefix('/')->middleware([
    // ← أضف اسم الـ middleware بعد ما تسجله في Kernel.php
    // 'EcommerceSecurityMiddleware',
    'ecommerce.security',
])->group(function () {

    // ── الرئيسية ──────────────────────────────
    Route::get('/', [EcommerceWelcomeController::class, 'index'])->name('home');
    Route::get('/profile', [EcommerceWelcomeController::class, 'index'])->name('profile.edit');

    // ── Auth ───────────────────────────────────
    Route::prefix('Customer')->group(function () {

        Route::prefix('Login')->group(function () {
            Route::get('/', [CustomerLoginController::class, 'CustomerLogin'])
                ->name('CustomerLogin');
            Route::post('/', [CustomerLoginController::class, 'CustomerLoginPost'])
                ->name('CustomerLoginPost')
                ->middleware('throttle:5,1'); // ← 5 محاولات per minute
        });

        Route::prefix('New')->group(function () {
            Route::get('/', [CustomerLoginController::class, 'CustomerLogin'])
                ->name('NewCustomer');
            Route::post('/', [NewCustomerController::class, 'NewCustomerPost'])
                ->name('NewCustomerPost')
                ->middleware('throttle:5,1');
        });

        Route::prefix('Code')->group(function () {
            Route::get('/', [CustomerLoginController::class, 'CustomerLogin'])
                ->name('CustomerCode');
            Route::post('/', [CustomerCodeController::class, 'CustomerCodePost'])
                ->name('CustomerCodePost')
                ->middleware('throttle:5,1');
        });

        Route::prefix('Welcome')->group(function () {
            Route::get('/', [CustomerWelcomeController::class, 'CustomerWelcome'])
                ->name('CustomerWelcome');
        });
    });

    // ── المنتجات ───────────────────────────────
    Route::prefix('Products')->group(function () {
        Route::prefix('All')->group(function () {
            Route::get('/', [EcommerceAllProductsController::class, 'EcommerceAllProducts'])
                ->name('EcommerceAllProducts');
        });

        Route::prefix('MostSale')->group(function () {
            Route::get('/', [EcommerceMostSaleProductsController::class, 'EcommerceMostSaleProducts'])
                ->name('EcommerceMostSaleProducts');
        });

        Route::prefix('Offers')->group(function () {
            Route::get('/', [EcommerceProductsWithOffersController::class, 'EcommerceProductsWithOffers'])
                ->name('EcommerceProductsWithOffers');
        });
    });

    // ── تفاصيل المنتج ──────────────────────────
    Route::prefix('ProductDetails')->group(function () {
        Route::get('/{id}', [ProductDetailsController::class, 'ProductDetails'])
            ->name('ProductDetails')
            ->where('id', '[0-9]+'); // ← فقط أرقام في الـ ID

        Route::post('/{id}', [ProductDetailsController::class, 'ProductDetailsPost'])
            ->name('ProductDetailsPost')
            ->where('id', '[0-9]+')
            ->middleware('throttle:10,1');

        Route::prefix('Comment')->group(function () {
            Route::post('/{id}', [ProductDetailsController::class, 'CustomerAddProductCommentPost'])
                ->name('CustomerAddProductCommentPost')
                ->where('id', '[0-9]+')
                ->middleware('throttle:3,1'); // ← 3 تعليقات per minute
        });
    });

    // ── التصنيفات ──────────────────────────────
    Route::prefix('CategoryProduct')->group(function () {
        Route::get('/{id}', [CategoryProductController::class, 'CategoryProduct'])
            ->name('CategoryProduct')
            ->where('id', '[0-9]+');
    });

    Route::prefix('SubcategoryProduct')->group(function () {
        Route::get('/{id}', [SubcategoryProductController::class, 'SubcategoryProduct'])
            ->name('SubcategoryProduct')
            ->where('id', '[0-9]+');
    });

    Route::prefix('CompanyProduct')->group(function () {
        Route::get('/{id}', [CompanyProductController::class, 'CompanyProduct'])
            ->name('CompanyProduct')
            ->where('id', '[0-9]+');
    });

    Route::prefix('Categories')->group(function () {
        Route::get('/', [EcommerceAllCategoriesController::class, 'EcommerceAllCategories'])
            ->name('EcommerceAllCategories');
    });

    // ── الصفحات الثابتة ────────────────────────
    Route::prefix('KnowAboutUs')->group(function () {
        Route::get('/', [EcommerceKnowAboutUsController::class, 'EcommerceKnowAboutUs'])
            ->name('EcommerceKnowAboutUs');
    });

    Route::prefix('ContactUs')->group(function () {
        Route::get('/', [EcommerceContactUsController::class, 'EcommerceContactUs'])
            ->name('EcommerceContactUs');
        Route::post('/', [EcommerceContactUsController::class, 'CustomerContactUsMessages'])
            ->name('CustomerContactUsMessages')
            ->middleware('throttle:3,60'); // ← 3 رسائل per hour
    });

    Route::prefix('Offers')->group(function () {
        Route::get('/', [EcommerceOffersController::class, 'EcommerceOffers'])
            ->name('EcommerceOffers');
    });

    // ── السلة ─────────────────────────────────
    /*
     | السلة بتشتغل بدون تسجيل دخول
     | throttle أعلى عشان UX أفضل
     | لكن الـ Controller بيتحقق من الـ limits
    */
    Route::get('/cart', [ShoppingCartController::class, 'ShoppingCart'])
        ->name('ShoppingCart');

    Route::post('/cart/add/{id}', [ShoppingCartController::class, 'CustomerRequestIncreseQuantityPost'])
        ->name('cart.add')
        ->where('id', '[0-9]+')
        ->middleware('throttle:30,1'); // 30 إضافة per minute

    Route::patch('/cart/update/{id}', [ShoppingCartController::class, 'CustomerRequestDecreaseQuantityPost'])
        ->name('cart.update')
        ->where('id', '[0-9]+')
        ->middleware('throttle:60,1'); // 60 تعديل per minute (الـ qty buttons بتضرب كتير)

    Route::delete('/cart/remove/{id}', [ShoppingCartController::class, 'CustomerRequestDeletePost'])
        ->name('cart.remove')
        ->where('id', '[0-9]+')
        ->middleware('throttle:20,1');

    // Routes القديمة (محتفظ بيها للـ backward compatibility)
    Route::prefix('ShoppingCart')->group(function () {
        Route::get('/', [ShoppingCartController::class, 'ShoppingCart'])
            ->name('ShoppingCartOld');

        Route::prefix('Increase')->group(function () {
            Route::post('/{id}', [ShoppingCartController::class, 'CustomerRequestIncreseQuantityPost'])
                ->name('CustomerRequestIncreseQuantityPost')
                ->where('id', '[0-9]+')
                ->middleware('throttle:30,1');
        });

        Route::prefix('Decrease')->group(function () {
            Route::post('/{id}', [ShoppingCartController::class, 'CustomerRequestDecreaseQuantityPost'])
                ->name('CustomerRequestDecreaseQuantityPost')
                ->where('id', '[0-9]+')
                ->middleware('throttle:60,1');
        });

        Route::prefix('Delete')->group(function () {
            Route::post('/{id}', [ShoppingCartController::class, 'CustomerRequestDeletePost'])
                ->name('CustomerRequestDeletePost')
                ->where('id', '[0-9]+')
                ->middleware('throttle:20,1');
        });
    });

    // ── Checkout ───────────────────────────────
    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout');

    Route::post('/checkout/confirm', [CheckoutController::class, 'confirm'])
        ->name('checkout.confirm')
        ->middleware('throttle:5,1'); // 5 تأكيدات per minute

    // ── المفضلة ───────────────────────────────
    Route::get('/wishlist', [WishlistController::class, 'index'])
        ->name('Wishlist');

    Route::post('/wishlist/toggle/{id}', [WishlistController::class, 'toggle'])
        ->name('wishlist.toggle')
        ->where('id', '[0-9]+')
        ->middleware('throttle:30,1');

    // ── الصفحة الشخصية ────────────────────────
    Route::prefix('PersonalPage')->group(function () {
        Route::prefix('Info')->group(function () {
            Route::get('/', [UserPersonalPageController::class, 'UserPersonalPage'])
                ->name('UserPersonalPage');
            Route::get('/{id}', [UserPersonalPageController::class, 'UserPersonalPage'])
                ->name('UserPersonalPageID')
                ->where('id', '[0-9]+');
            Route::post('/{id}', [UserPersonalPageController::class, 'UserPersonalPageIDPost'])
                ->name('UserPersonalPageIDPost')
                ->where('id', '[0-9]+')
                ->middleware('throttle:10,1');
        });

        Route::prefix('ShipmentProducts')->group(function () {
            Route::get('/', [UserPersonalPageController::class, 'UserPersonalUnderRequstProducts'])
                ->name('UserPersonalUnderRequstProducts');
        });

        Route::prefix('Statement')->group(function () {
            Route::get('/', [UserPersonalPageController::class, 'UserPersonalStatement'])
                ->name('UserPersonalStatement');
        });

        Route::prefix('LogOut')->group(function () {
            Route::get('/', [UserPersonalPageController::class, 'UserPersonalLogOut'])
                ->name('UserPersonalLogOut');
        });
    });

    // ── الصيانة ───────────────────────────────
    Route::prefix('Maintenance')->group(function () {
        Route::get('/', [UserMaintenanceController::class, 'UserMaintenance'])
            ->name('UserMaintenance');
    });

    // ── Privacy ───────────────────────────────
    Route::prefix('PrivacyPolicy')->group(function () {
        Route::get('/', [PrivacyPolicyController::class, 'PrivacyPolicy'])
            ->name('PrivacyPolicy');
    });

    Route::prefix('TermsAndConditions')->group(function () {
        Route::get('/', [PrivacyPolicyController::class, 'TermsAndConditions'])
            ->name('TermsAndConditions');
    });
});
// 
// fix 1
// ── Add to Cart ───────────────────────────────────
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-add-to-cart');
    if (!btn) return; // ← الـ return هنا بيوقف التنفيذ بدون preventDefault
    
    e.preventDefault(); // ← بس هنا لما يكون btn موجود فعلاً
    
    // ... باقي الكود
});

// ── Wishlist Toggle ───────────────────────────────
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-wishlist-toggle');
    if (!btn) return; // ← نفس الفكرة
    
    e.preventDefault();
    
    // ... باقي الكود
});

// ── Save for Later ────────────────────────────────
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-save-later');
    if (!btn) return;
    
    e.preventDefault();
    
    // ... باقي الكود
});

// ── Cart Qty ──────────────────────────────────────
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-qty-btn');
    if (!btn) return;
    
    e.preventDefault();
    
    // ... باقي الكود
});

// ── Delete Confirm ────────────────────────────────
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-delete-confirm');
    if (!btn) return;
    
    e.preventDefault();
    
    // ... باقي الكود
});

// fix ma
بعد مراجعة أعمق، المشكلة في EcommerceSecurityMiddleware — في السطر ده:
php// في EcommerceSecurityMiddleware.php
private const SUSPICIOUS_PATTERNS = [
    // ...
    '/on\w+\s*=/i',    // ← ده بيبلوك onclick=, onload=, etc.
    // ...
];
الـ pattern ده بيبلوك أي GET parameter فيه on + حروف + =.
لو الـ URL بتاع صفحة المنتجات فيه parameters زي &color= أو &sort=price_desc، ممكن يتطابق مع الـ pattern ده ويبلوك الـ request — وده بيأثر على Bootstrap لأن الصفحة مش بتشحن صح.

الحل الصح — عدّل الـ Pattern
php// في app/Http/Middleware/EcommerceSecurityMiddleware.php

private const SUSPICIOUS_PATTERNS = [
    '/(\bUNION\b.*\bSELECT\b|\bSELECT\b.*\bFROM\b)/i',
    '/(\bDROP\b|\bDELETE\b|\bTRUNCATE\b)\s+\bTABLE\b/i',
    '/<script[\s>]/i',
    '/javascript\s*:/i',
    // ← عدّلنا الـ pattern ده — كان بيبلوك حاجات عادية
    // '/on\w+\s*=/i',   // ← احذف أو علّق عليه
    '/\.\.\//i',
    '/etc\/passwd/i',
    '/proc\/self/i',
];
ليه شيلنا on\w+\s*=/i؟
لأنه بيبلوك GET params زي:

?color=أحمر ← color= بيطابق on\w+=؟ لا بس ممكن
?sort=price_desc — لو فيه on في أي حتة في الـ value

الـ pattern ده مفيد في HTML لكن مش مفيد في URL params.

الاختبار السريع
افتح الـ DevTools وشوف الـ Console لما تضغط على الـ dropdown:
javascript// في Console
document.querySelector('.dropdown-toggle')?.click();
// لو مفيش error يبقى المشكلة في الـ CSS مش الـ JS

// تحقق إن Bootstrap محمّل
console.log(typeof bootstrap); // المفروض يطلع "object"

// تحقق إن الـ dropdown elements موجودة
console.log(document.querySelectorAll('[data-bs-toggle="dropdown"]').length);
لو typeof bootstrap رجع "undefined" — يبقى Bootstrap مش بيتحمل، وده بيكون بسبب error في الـ app.js بيمنع Bootstrap من التحميل.
شوف الـ Console في الـ browser — هل في أي error بالون أحمر قبل الـ dropdown error؟