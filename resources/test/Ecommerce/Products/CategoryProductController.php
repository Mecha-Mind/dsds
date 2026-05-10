<?php

namespace App\Http\Controllers\Ecommerce\Products;
use App\Services\EcommerceSharedDataService;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceCompany;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryProductController extends Controller
{
    public function CategoryProduct($id)
    {
        
        $Category = Category::where('category_id', $id)->first();

        if (!$Category) {
            return back()->withErrors(" هذا التصنيف غير متاح حاليا ");
        }

        $Productsids = Product::where('product_category', $id)
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


        $Subcategories = Subcategory::where('subcategory_displaystatus', '1')
            ->where('subcategory_category', $id)
            ->orderBy('updated_at', 'desc')
            ->get();
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'جميع التصنيفات';

        return view('ecommerce.CategoryProduct.CategoryProduct', compact(
            'Products',
            'AllEcommerceProducts',
            'ecommerceSharedData',
            'PartnerCompanies',
            'Category',
            'Subcategories'
        ));
    }

    public function CategoryProductRow($id)
    {

        $Category = Category::where('category_id', $id)->first();

        if (!$Category) {
            return back()->withErrors(" هذا التصنيف غير متاح حاليا ");
        }

        $Productsids = Product::where('product_category', $id)
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

        $Subcategories = Subcategory::where('subcategory_displaystatus', '1')
            ->where('subcategory_category', $id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('ecommerce.CategoryProduct.CategoryProductRow', compact(
            'Products',
            'AllEcommerceProducts',
            'ecommerceSharedData',
            'PartnerCompanies',
            'Category',
            'Subcategories'
        ));
    }
}
