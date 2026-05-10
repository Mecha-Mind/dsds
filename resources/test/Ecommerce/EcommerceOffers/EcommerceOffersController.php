<?php

namespace App\Http\Controllers\Ecommerce\EcommerceOffers;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\Category;
use App\Models\EcommerceProduct;
use App\Models\Employee;
use App\Models\MaintenanceCompany;
use App\Models\Offersfromtheowner;
use App\Models\Product;
use App\Services\EcommerceSharedDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EcommerceOffersController extends Controller
{
    public function EcommerceOffers()
    {

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'العروض';
        $AboutUs = AboutUs::where('id', '1')->first();

        $Employees = Employee::where('employee_displaystatus', '1')
            ->whereNotNull('employee_image')
            ->get();

        $Offersfromtheowners = Offersfromtheowner::where('offerfromtheowner_active', '1')
            ->whereNotNull('offerfromtheowner_image')
            ->orderBy('updated_at', 'desc')
            ->paginate(8);


        return view('ecommerce.EcommerceOffers.EcommerceOffers', compact(
            'Employees',
            'AboutUs',
            'ecommerceSharedData',
            'Offersfromtheowners'
        ));
    }
}
