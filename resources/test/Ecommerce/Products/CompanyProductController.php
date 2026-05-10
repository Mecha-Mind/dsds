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

class CompanyProductController extends Controller
{
    public function CompanyProduct($id)
    {
        
        $Company = MaintenanceCompany::where('maintenancecompany_id', $id)
            ->where('maintenancecompany_active', '1')
            ->first();

        if (!$Company) {
            return back()->withErrors(" هذا الشركة غير متاحة غير متاح حاليا ");
        }

        $Productsids = Product::where('product_maintenancecompany', $id)
            ->pluck('product_id');

        $Products = Product::whereIn('product_id', $Productsids)
            ->get();

        $AllEcommerceProducts = EcommerceProduct::whereIn('product_id', $Productsids)
            ->where('ecommerceproduct_displaystatus', '1')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->orderBy('updated_at', 'desc')
            ->get();
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'جميع التصنيفات';

        return view('ecommerce.CompanyProduct.CompanyProduct', compact(
            'Products',
            'AllEcommerceProducts',
            'ecommerceSharedData',
            'PartnerCompanies',
            'Company'
        ));
    }

    public function CompanyProductRow($id)
    {

        $Company = MaintenanceCompany::where('maintenancecompany_id', $id)
            ->where('maintenancecompany_active', '1')
            ->first();

        if (!$Company) {
            return back()->withErrors(" هذا التصنيف غير متاح حاليا ");
        }

        $Productsids = Product::where('product_maintenancecompany', $id)
            ->pluck('product_id');

        $Products = Product::whereIn('product_id', $Productsids)
            ->get();

        $AllEcommerceProducts = EcommerceProduct::whereIn('product_id', $Productsids)
            ->where('ecommerceproduct_displaystatus', '1')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->orderBy('updated_at', 'desc')
            ->get();
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'جميع التصنيفات';
        return view('ecommerce.CompanyProduct.CompanyProductRow', compact(
            'Products',
            'AllEcommerceProducts',
            'ecommerceSharedData',
            'PartnerCompanies',
            'Company'
        ));
    }
}
