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
