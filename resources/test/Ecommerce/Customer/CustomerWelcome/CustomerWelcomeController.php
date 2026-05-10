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
