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
