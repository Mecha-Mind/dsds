<?php

namespace App\Http\Controllers\Ecommerce\EcommerceKnowAboutUs;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\Branche;
use App\Models\Category;
use App\Models\EcommerceProduct;
use App\Models\Employee;
use App\Models\MaintenanceCompany;
use App\Models\Product;
use App\Models\SocialMediaContact;
use App\Services\EcommerceSharedDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EcommerceKnowAboutUsController extends Controller
{
    public function EcommerceKnowAboutUs()
    {
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'من نحن';
        
        $AboutUs = AboutUs::where('id', '1')->first();

        $Employees = Employee::where('employee_displaystatus', '1')
            ->whereNotNull('employee_image')
            ->get();

        return view('ecommerce.EcommerceKnowAboutUs.EcommerceKnowAboutUs', compact(
            'Employees',
            'AboutUs',
            'ecommerceSharedData',
        ));
    }
}
