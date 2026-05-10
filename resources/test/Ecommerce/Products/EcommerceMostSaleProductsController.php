<?php

namespace App\Http\Controllers\Ecommerce\Products;

use App\Services\EcommerceSharedDataService;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceCompany;
use App\Models\SocialMediaContact;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EcommerceMostSaleProductsController extends Controller
{
    public function EcommerceMostSaleProducts()
    {


        $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->orderBy('updated_at', 'desc')
            ->get();

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'الأكثر مبيعا';

        $Products = Product::all();

        $AllEcommerceProducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->where('ecommerceproduct_appearinbestsellers', '1')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('ecommerce.EcommerceAllProducts.EcommerceMostSaleProducts', compact('Products', 'AllEcommerceProducts', 'ecommerceSharedData', 'PartnerCompanies'));
    }

    public function EcommerceMostSaleProductsRow()
    {


        $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->orderBy('updated_at', 'desc')
            ->take(6)

            ->get();

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'الأكثر مبيعا';

        $Products = Product::all();

        $AllEcommerceProducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->where('ecommerceproduct_appearinbestsellers', '1')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('ecommerce.EcommerceAllProducts.EcommerceMostSaleProductsRow', compact('Products', 'AllEcommerceProducts', 'ecommerceSharedData', 'PartnerCompanies'));
    }
}
