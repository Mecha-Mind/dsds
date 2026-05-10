<?php

namespace App\Http\Controllers\Ecommerce\Products;

use App\Services\EcommerceSharedDataService;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceCompany;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EcommerceProductsWithOffersController extends Controller
{
    public function EcommerceProductsWithOffers()
    {
        $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->orderBy('updated_at', 'desc')
            ->get();

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'العروض';

        $Products = Product::all();

        $AllEcommerceProducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->where('ecommerceproduct_appearinthelistofoffers', '1')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('ecommerce.EcommerceAllProducts.EcommerceProductsWithOffers', compact('Products', 'AllEcommerceProducts', 'ecommerceSharedData', 'PartnerCompanies'));
    }

    public function EcommerceProductsWithOffersRow()
    {


        $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->orderBy('updated_at', 'desc')
            ->get();

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'العروض';

        $Products = Product::all();

        $AllEcommerceProducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->where('ecommerceproduct_appearinthelistofoffers', '1')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('ecommerce.EcommerceAllProducts.EcommerceProductsWithOffersRow', compact('Products', 'AllEcommerceProducts', 'ecommerceSharedData', 'PartnerCompanies'));
    }
}
