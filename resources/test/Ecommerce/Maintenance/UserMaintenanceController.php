<?php

namespace App\Http\Controllers\Ecommerce\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceOrders;
use App\Models\Product;
use App\Models\SaleProductBill;
use App\Models\Transaction;
use App\Services\EcommerceSharedDataService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class UserMaintenanceController extends Controller
{


    public function UserMaintenance()
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
        $ecommerceSharedData['pageTitle'] = 'الصيانة الخاصة';

        $customer_account = $Customer->customer_account;
        $customer_phone = $Customer->customer_phone;

        $MaintenanceOrders = MaintenanceOrders::where(function ($query) use ($customer_account, $customer_phone) {
            $query->where('maintenanceorder_ordercustomeraccount', $customer_account)
                ->orWhere('maintenanceorder_ordercustomeraccount', $customer_phone);
        })
            ->whereNotIn('maintenanceorder_orderstatus', ['delivered', 'surveied'])
            ->orderBy('maintenanceorder_id', 'asc')
            ->paginate(100);

        return view('ecommerce.Maintenance.UserMaintenance', compact(
            'ecommerceSharedData',
            'Customer',
            'MaintenanceOrders'

        ));
    }
}
