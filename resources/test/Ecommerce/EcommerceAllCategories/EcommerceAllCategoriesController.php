<?php

namespace App\Http\Controllers\Ecommerce\EcommerceAllCategories;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceCompany;
use App\Models\Product;
use App\Services\EcommerceSharedDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EcommerceAllCategoriesController extends Controller
{
    public function EcommerceAllCategories()
    {

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'جميع التصنيفات';
      
        // ── الشركات/الماركات ──
        $FilterBrands = MaintenanceCompany::where('maintenancecompany_active', 1)
            ->where('maintenancecompany_delete', '0')
            ->get();

        $Categories = Category::where('category_displaystatus', '1')
            ->orderBy('updated_at', 'desc')
            ->get();



        return view('ecommerce.EcommerceAllCategories.EcommerceAllCategories', compact(
            'ecommerceSharedData',
            'FilterBrands',
            'Categories',
        ));
    }
}
