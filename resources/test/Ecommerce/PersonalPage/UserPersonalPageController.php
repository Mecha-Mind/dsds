<?php

namespace App\Http\Controllers\Ecommerce\PersonalPage;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Customer;
use App\Models\EcommerceProduct;
use App\Models\Product;
use App\Models\SaleProductBill;
use App\Models\Transaction;
use App\Services\EcommerceSharedDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserPersonalPageController extends Controller
{
    public function UserPersonalPage()
    {

        $customerPhone = session('customer_phone');

        if ($customerPhone  == null) {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' يجب تسجيل الدخول اول حتي نتمكن من روائية طلبك ']);
        }

        $Customer = Customer::where('customer_phone', $customerPhone)->first();

        if (!$Customer) {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' يجب انشاء حساب اول حتي نتمكن من الدخول الي عربة التسوق الخاصة بك ']);
        }

        if ($Customer->customer_delete == '1' || $Customer->customer_block == '1') {
            return redirect()
                ->route('home')
                ->withErrors([' تم حظر حسابك , يرجي التواصل مع خدمة العملاء ']);
        }
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'صفحة المستخدم الشخصية';

        return view('ecommerce.PersonalPage.Info', compact(
            'ecommerceSharedData',
            'Customer',
        ));
    }

    public function UserPersonalPageIDPost(Request $request, $id)
    {
        try {
            // ✅ تحقق من أن المستخدم المسجل هو صاحب الحساب فعلاً
            $customerPhone = session('customer_phone');

            $Customer = Customer::where([
                ['customer_block', 0],
                ['customer_delete', 0],
                ['customer_id', $id],
                ['customer_phone', $customerPhone],
            ])->first();

            if (!$Customer) {
                return back()->withErrors('⚠️ يجب تحديث بياناتك الشخصية فقط وليس لحساب آخر');
            }

            $validator = Validator::make($request->all(), [
                'customer_name' => 'required|string|max:255',
                'customer_email' => [
                    'nullable',
                    'email',
                    'max:255',
                    Rule::unique('customers', 'customer_email')->ignore($id, 'customer_id')
                ],
                'customer_phone' => [
                    'required',
                    'string',
                    'regex:/^01[0-9]{9}$/',
                    'max:11',
                    Rule::unique('customers', 'customer_phone')->ignore($id, 'customer_id')
                ],
                'customer_phone2' => 'nullable|string|regex:/^01[0-9]{9}$/|max:11',
                'customer_address' => 'nullable|string|max:255',
                'customer_telegramchatid' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('customers', 'customer_telegramchatid')->ignore($id, 'customer_id')
                ],
                'customer_password' => 'nullable',
                'string',
                'min:6',
                'max:255',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            if ($request->customer_password && $request->customer_password != null) {
                $customer_password = Hash::make($request->customer_password);
            } else {
                $customer_password = $Customer->customer_password;
            }

            // ✅ تنفيذ التحديث داخل معاملة (Transaction)
            DB::beginTransaction();

            if ($Customer->customer_account && $Account = Account::find($Customer->customer_account)) {
                $Account->account_type    = 'person';
                $Account->account_phone   = $request->customer_phone;
                $Account->account_name    = $request->customer_name;
                $Account->account_level_1 = 'الاصول';
                $Account->account_level_2 = 'الاصول المتداولة';
                $Account->account_level_3 = 'العملاء';
                $Account->account_level_4 = $request->customer_name;
                $Account->user_name       = 'Customer_' . $request->customer_phone;
                $Account->save();
            }

            $Customer->customer_name           = $request->customer_name;
            $Customer->customer_email          = $request->customer_email;
            $Customer->customer_phone          = $request->customer_phone;
            $Customer->customer_phone2         = $request->customer_phone2;
            $Customer->customer_address        = $request->customer_address;
            $Customer->customer_telegramchatid = $request->customer_telegramchatid;
            $Customer->customer_password       = $customer_password;
            $Customer->user_name               = 'Customer_' . $request->customer_phone;
            $Customer->save();

            DB::commit();

            return back()->with('success', '✅ تم التحديث بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }


    public function UserPersonalUnderRequstProducts()
    {

        $customerPhone = session('customer_phone');

        if ($customerPhone  == null) {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' يجب تسجيل الدخول اول حتي نتمكن من روائية طلبك ']);
        }

        $Customer = Customer::where('customer_phone', $customerPhone)->first();

        if (!$Customer) {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' يجب انشاء حساب اول حتي نتمكن من الدخول الي عربة التسوق الخاصة بك ']);
        }

        if ($Customer->customer_delete == '1' || $Customer->customer_block == '1') {
            return redirect()
                ->route('home')
                ->withErrors([' تم حظر حسابك , يرجي التواصل مع خدمة العملاء ']);
        }

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'صفحة المستخدم الشخصية';

        $customer_account = $Customer->customer_account;
        $customer_phone = $Customer->customer_phone;
        if (substr($customer_phone, 0, 1) === '0') {
            $customer_phone = substr($customer_phone, 1);
        }

        // dd($customer_phone);
        $CustomerSaleProductBillProducts = SaleProductBill::where(function ($query) use ($customer_account, $customer_phone) {
            $query->where('saleproductbill_customeraccount', $customer_account)
                ->orWhere('saleproductbill_customeraccount', $customer_phone);
        })
            ->whereNotNull('saleproductbill_productname')
            ->whereNull('saleproductbill_paidbilldatetime')
            ->orderBy('id', 'desc')
            ->get();

        $OrderConfirmationTime = null;
        $TimePrepareShipping = null;
        $CustomerBillRefrance = null;

        if ($CustomerSaleProductBillProducts->isNotEmpty()) {
            $FirstSaleProductBillTime = SaleProductBill::where(function ($query) use ($customer_account, $customer_phone) {
                $query->where('saleproductbill_customeraccount', $customer_account)
                    ->orWhere('saleproductbill_customeraccount', $customer_phone);
            })
                ->whereNull('saleproductbill_paidbilldatetime')
                ->first();

            $LastSaleProductBillTime = SaleProductBill::where(function ($query) use ($customer_account, $customer_phone) {
                $query->where('saleproductbill_customeraccount', $customer_account)
                    ->orWhere('saleproductbill_customeraccount', $customer_phone);
            })
                ->whereNull('saleproductbill_paidbilldatetime')
                ->orderBy('id', 'desc')
                ->first();

            $OrderConfirmationTime = $FirstSaleProductBillTime->created_at;
            $TimePrepareShipping = $LastSaleProductBillTime->updated_at;
            $CustomerBillRefrance = $LastSaleProductBillTime->saleproductbill_billreference;
        }


        $CustomerSaleProductBillProductsIds = SaleProductBill::where(function ($query) use ($customer_account, $customer_phone) {
            $query->where('saleproductbill_customeraccount', $customer_account)
                ->orWhere('saleproductbill_customeraccount', $customer_phone);
        })
            ->whereNotNull('saleproductbill_productname')
            ->whereNull('saleproductbill_paidbilldatetime')
            ->orderBy('id', 'desc')
            ->pluck('saleproductbill_productname');

        $AllEcommerceProducts = EcommerceProduct::whereIn('product_id', $CustomerSaleProductBillProductsIds)
            ->paginate(100);

        $Products = Product::whereIn('product_id', $CustomerSaleProductBillProductsIds)
            ->get();

        $ProductsById = collect($Products)->keyBy('product_id');
        $CustomerSaleProductBillProductsId = collect($CustomerSaleProductBillProducts)->keyBy('saleproductbill_productname');

        return view('ecommerce.PersonalPage.ShipmentProducts', compact(
            'ecommerceSharedData',
            'Customer',
            'CustomerSaleProductBillProducts',
            'AllEcommerceProducts',
            'Products',
            'OrderConfirmationTime',
            'TimePrepareShipping',
            'CustomerBillRefrance'
        ));
    }

    public function UserPersonalStatement()
    {

        $customerPhone = session('customer_phone');

        if ($customerPhone  == null) {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' يجب تسجيل الدخول اول حتي نتمكن من روائية طلبك ']);
        }

        $Customer = Customer::where('customer_phone', $customerPhone)->first();

        if (!$Customer) {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' يجب انشاء حساب اول حتي نتمكن من الدخول الي عربة التسوق الخاصة بك ']);
        }

        if ($Customer->customer_delete == '1' || $Customer->customer_block == '1') {
            return redirect()
                ->route('home')
                ->withErrors([' تم حظر حسابك , يرجي التواصل مع خدمة العملاء ']);
        }

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'صفحة المستخدم الشخصية';

        $customer_account = $Customer->customer_account;
        $customer_phone = $Customer->customer_phone;
        if (substr($customer_phone, 0, 1) === '0') {
            $customer_phone = substr($customer_phone, 1);
        }

        $CustomerSaleProductBillProducts = SaleProductBill::where(function ($query) use ($customer_account, $customer_phone) {
            $query->where('saleproductbill_customeraccount', $customer_account)
                ->orWhere('saleproductbill_customeraccount', $customer_phone);
        })
            ->whereNotNull('saleproductbill_productname')
            ->orderBy('id', 'desc')
            ->paginate(100);

        $CustomerSaleProductBillProductsIds = SaleProductBill::where(function ($query) use ($customer_account, $customer_phone) {
            $query->where('saleproductbill_customeraccount', $customer_account)
                ->orWhere('saleproductbill_customeraccount', $customer_phone);
        })
            ->whereNotNull('saleproductbill_productname')
            ->orderBy('id', 'desc')
            ->pluck('saleproductbill_productname');

        $AllEcommerceProducts = EcommerceProduct::whereIn('product_id', $CustomerSaleProductBillProductsIds)
            ->get();

        $Products = Product::whereIn('product_id', $CustomerSaleProductBillProductsIds)
            ->get();

        $ProductsById = collect($Products)->keyBy('product_id');
        $AllEcommerceProductsId = collect($AllEcommerceProducts)->keyBy('product_id');

        $SaleProductBillsDetails = SaleProductBill::where(function ($query) use ($customer_account, $customer_phone) {
            $query->where('saleproductbill_customeraccount', $customer_account)
                ->orWhere('saleproductbill_customeraccount', $customer_phone);
        })
            ->orderBy('updated_at', 'desc')
            ->whereNotNull('saleproductbill_productname')
            ->get()
            ->groupBy('saleproductbill_billreference');

        $CustomerSaleProductBillRowCounts = SaleProductBill::where(function ($query) use ($customer_account, $customer_phone) {
            $query->where('saleproductbill_customeraccount', $customer_account)
                ->orWhere('saleproductbill_customeraccount', $customer_phone);
        })
            ->whereNotNull('saleproductbill_productname')
            ->count();

        $CustomerSaleProductBillQauntityCounts = SaleProductBill::where(function ($query) use ($customer_account, $customer_phone) {
            $query->where('saleproductbill_customeraccount', $customer_account)
                ->orWhere('saleproductbill_customeraccount', $customer_phone);
        })
            ->whereNotNull('saleproductbill_productname')
            ->sum('saleproductbill_productquantity');

        $CustomerSaleProductBillProductsPrices = SaleProductBill::where(function ($query) use ($customer_account, $customer_phone) {
            $query->where('saleproductbill_customeraccount', $customer_account)
                ->orWhere('saleproductbill_customeraccount', $customer_phone);
        })
            ->whereNotNull('saleproductbill_productname')
            ->sum('saleproductbill_producttotalquantityprice');

        $CustomerSaleProductBillReferances = SaleProductBill::where(function ($query) use ($customer_account, $customer_phone) {
            $query->where('saleproductbill_customeraccount', $customer_account)
                ->orWhere('saleproductbill_customeraccount', $customer_phone);
        })
            ->whereNotNull('saleproductbill_productname')
            ->orderBy('id', 'desc')
            ->get()
            ->unique('saleproductbill_billreference');

        $saleproductbill_incomeservicesamount = 0;
        $totalsaleproductbill_incomeservicesamount = 0;

        $saleproductbill_salebillpricediscountamount = 0;
        $totalsaleproductbill_salebillpricediscountamount = 0;

        foreach ($CustomerSaleProductBillReferances as $CustomerSaleProductBillReferance) {
            $saleproductbill_incomeservicesamount = $CustomerSaleProductBillReferance->saleproductbill_incomeservicesamount;
            $totalsaleproductbill_incomeservicesamount = $totalsaleproductbill_incomeservicesamount + $saleproductbill_incomeservicesamount;

            $saleproductbill_salebillpricediscountamount = $CustomerSaleProductBillReferance->saleproductbill_salebillpricediscountamount;
            $totalsaleproductbill_salebillpricediscountamount = $totalsaleproductbill_salebillpricediscountamount + $saleproductbill_salebillpricediscountamount;
        }


        $TotalAfterAiscountAndServiceAddition = ($CustomerSaleProductBillProductsPrices + $totalsaleproductbill_incomeservicesamount) - $totalsaleproductbill_salebillpricediscountamount;

        // الحسابات

        // المدفوع في فاتورة المبعات

        // get the last rows for each transaction_bill where transaction_remark contains 'استلام المدفوعات النقدية' and 'استلام المدفوعات اللكترونية'

        $CustomerSaleTransactionsBills = SaleProductBill::where(function ($query) use ($customer_account, $customer_phone) {
            $query->where('saleproductbill_customeraccount', $customer_account)
                ->orWhere('saleproductbill_customeraccount', $customer_phone);
        })
            ->orderBy('id', 'desc')
            ->pluck('saleproductbill_billreference');

        $TransactionCashsInSales = Transaction::where(function ($query) use ($customer_account) {
            $query->where('transaction_credit_account', $customer_account)
                ->orWhere('transaction_depit_account', $customer_account);
        })
            ->whereIn('transaction_bill', $CustomerSaleTransactionsBills)
            ->where(function ($query) {
                $query->where('transaction_remark', 'like', '%استلام المدفوعات النقدية%');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('transaction_bill')
            ->map(function ($group) {
                return $group->first();
            });

        $TransactionsElectronicsInSales = Transaction::where(function ($query) use ($customer_account) {
            $query->where('transaction_credit_account', $customer_account)
                ->orWhere('transaction_depit_account', $customer_account);
        })
            ->whereIn('transaction_bill', $CustomerSaleTransactionsBills)
            ->where('transaction_remark', 'like', '%استلام المدفوعات اللكترونية%')
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($electronicTx) use ($TransactionCashsInSales) {
                $bill = $electronicTx->transaction_bill;

                if (!isset($TransactionCashsInSales[$bill])) {
                    return false;
                }

                $cashTx = $TransactionCashsInSales[$bill];

                // نحسب الفرق بالدقائق بين الوقتين
                $cashTime = Carbon::parse($cashTx->created_at);
                $electronicTime = Carbon::parse($electronicTx->created_at);

                $diffInMinutes = abs($cashTime->diffInMinutes($electronicTime));

                return $diffInMinutes <= 2;
            })
            ->groupBy('transaction_bill')
            ->map(function ($group) {
                return $group->first(); // أول إلكتروني مطابق للكاش في نفس الفاتورة خلال دقيقتين
            });



        // ايراد نقدي 

        $TransactionCashsInSafes = Transaction::where(function ($query) use ($customer_account) {
            $query->where('transaction_credit_account', $customer_account)
                ->orWhere('transaction_depit_account', $customer_account);
        })
            ->where(function ($query) {
                $query->where('transaction_type', 'like', '%استلام من نقديه العميل%');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('transaction_bill')
            ->map(function ($group) {
                return $group->first();
            });

        $TransactionsElectronicsInSafes = Transaction::where(function ($query) use ($customer_account) {
            $query->where('transaction_credit_account', $customer_account)
                ->orWhere('transaction_depit_account', $customer_account);
        })
            ->where(function ($query) {
                $query->Where('transaction_type', 'like', '%استلام النقديه من الخدمه الالكترونية%');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('transaction_bill')
            ->map(function ($group) {
                return $group->first();
            });


        // مصروف نقدي

        $TransactionCashsOutSafes = Transaction::where(function ($query) use ($customer_account) {
            $query->where('transaction_credit_account', $customer_account)
                ->orWhere('transaction_depit_account', $customer_account);
        })
            ->where(function ($query) {
                $query->where('transaction_type', 'like', '%صرف نقديه العميل%');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('transaction_bill')
            ->map(function ($group) {
                return $group->first();
            });

        $TransactionsElectronicsOutSafes = Transaction::where(function ($query) use ($customer_account) {
            $query->where('transaction_credit_account', $customer_account)
                ->orWhere('transaction_depit_account', $customer_account);
        })
            ->where(function ($query) {
                $query->Where('transaction_type', 'like', '%صرف النقديه من الخدمه الالكترونية%');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('transaction_bill')
            ->map(function ($group) {
                return $group->first();
            });

        // ائتمان مباشر

        $TransactionsDirectCredit = Transaction::where(function ($query) use ($customer_account) {
            $query->where('transaction_credit_account', $customer_account)
                ->orWhere('transaction_depit_account', $customer_account);
        })
            ->where(function ($query) {
                $query->Where('transaction_type', 'like', '%اضافة الائتمان المباشر مسموح%');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('transaction_bill')
            ->map(function ($group) {
                return $group->first();
            });

        // خصم مباشر

        $TransactionsDirectDebit = Transaction::where(function ($query) use ($customer_account) {
            $query->where('transaction_credit_account', $customer_account)
                ->orWhere('transaction_depit_account', $customer_account);
        })
            ->where(function ($query) {
                $query->Where('transaction_type', 'like', '%تم صرف خصم مباشر مسموح%');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('transaction_bill')
            ->map(function ($group) {
                return $group->first();
            });


        $Transactions = $TransactionCashsInSales
            ->merge($TransactionsElectronicsInSales)
            ->merge($TransactionCashsInSafes)
            ->merge($TransactionsElectronicsInSafes)
            ->merge($TransactionCashsOutSafes)
            ->merge($TransactionsElectronicsOutSafes)
            ->merge($TransactionsDirectCredit)
            ->merge($TransactionsDirectDebit)
            ->sortByDesc('created_at')
            ->values();

        $TotalCustomerForHim = '0';
        $TotalCustomerAgainstHim = '0';

        foreach ($Transactions as $Transaction) {
            $TotalCustomerForHim = $TotalCustomerForHim + $Transaction->transaction_credit_cost;
            $TotalCustomerAgainstHim = $TotalCustomerAgainstHim + $Transaction->transaction_depit_cost;
        }




        $TheTotalCustomerRestAmount = ($TotalAfterAiscountAndServiceAddition + $TotalCustomerAgainstHim) - $TotalCustomerForHim;

        $TheTotalCustomerRestAmount = number_format($TheTotalCustomerRestAmount, 2);
        $totalsaleproductbill_incomeservicesamount = number_format($totalsaleproductbill_incomeservicesamount, 2);
        $totalsaleproductbill_salebillpricediscountamount = number_format($totalsaleproductbill_salebillpricediscountamount, 2);
        $TotalCustomerForHim = number_format($TotalCustomerForHim, 2);
        $TotalCustomerAgainstHim = number_format($TotalCustomerAgainstHim, 2);
        $TotalAfterAiscountAndServiceAddition = number_format($TotalAfterAiscountAndServiceAddition, 2);


        return view('ecommerce.PersonalPage.Statement', compact(
            'ecommerceSharedData',
            'Customer',
            'CustomerSaleProductBillProducts',
            'AllEcommerceProducts',
            'Products',
            'ProductsById',
            'AllEcommerceProductsId',
            'TotalAfterAiscountAndServiceAddition',
            'TotalCustomerForHim',
            'TotalCustomerAgainstHim',
            'TheTotalCustomerRestAmount',

        ));
    }

    public function UserPersonalLogOut()
    {

        $customerPhone = session('customer_phone');

        if ($customerPhone  == null) {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' يجب تسجيل الدخول اول حتي نتمكن من روائية طلبك ']);
        }

        $Customer = Customer::where('customer_phone', $customerPhone)->first();

        if (!$Customer) {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' يجب انشاء حساب اول حتي نتمكن من الدخول الي عربة التسوق الخاصة بك ']);
        }

        if ($Customer->customer_delete == '1' || $Customer->customer_block == '1') {
            return redirect()
                ->route('home')
                ->withErrors([' تم حظر حسابك , يرجي التواصل مع خدمة العملاء ']);
        }

        Auth::logout();
        Session::flush();

        return redirect()
            ->route('home')
            ->with('success', 'تم تسجيل الخروج بنجاح');
    }
}
