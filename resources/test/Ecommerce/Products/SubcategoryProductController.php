<?php

namespace App\Http\Controllers\Ecommerce\Products;

use App\Services\EcommerceSharedDataService;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceCompany;
use App\Models\SocialMediaContact;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SubcategoryProductController extends Controller
{
    public function SubcategoryProduct($id)
    {

        $Subcategory = Subcategory::where('subcategory_id', $id)->first();

        if (!$Subcategory) {
            return back()->withErrors(" هذا التصنيف غير متاح حاليا ");
        }

        $Productsids = Product::where('product_category2', $id)
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

        return view('ecommerce.SubcategoryProduct.SubcategoryProduct', compact(
            'Products',
            'AllEcommerceProducts',
            'ecommerceSharedData',
            'PartnerCompanies',
            'Subcategory'
        ));
    }

    public function SubcategoryProductRow($id)
    {

        $Subcategory = Subcategory::where('subcategory_id', $id)->first();

        if (!$Subcategory) {
            return back()->withErrors(" هذا التصنيف غير متاح حاليا ");
        }

        $Productsids = Product::where('product_category2', $id)
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

        return view('ecommerce.SubcategoryProduct.SubcategoryProductRow', compact(
            'Products',
            'AllEcommerceProducts',
            'ecommerceSharedData',
            'PartnerCompanies',
            'Subcategory',
        ));
    }
}
