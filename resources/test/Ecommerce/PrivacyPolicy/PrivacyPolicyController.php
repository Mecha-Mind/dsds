<?php

namespace App\Http\Controllers\Ecommerce\PrivacyPolicy;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Transaction;
use App\Services\EcommerceSharedDataService;
use Illuminate\Support\Carbon;
use App\Models\SaleProductBill;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceOrders;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class PrivacyPolicyController extends Controller
{


    public function PrivacyPolicy()
    {

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'الخصوصية';

        return view('ecommerce.PrivacyPolicy.PrivacyPolicy', compact(
            'ecommerceSharedData',
        ));
    }

    public function TermsAndConditions()
    {
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'الشروط و الأحكام';


        return view('ecommerce.TermsAndConditions.TermsAndConditions', compact(
            'ecommerceSharedData',
        ));
    }
}
