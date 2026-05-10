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
