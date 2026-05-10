<div id="desktop_content_EcommerceAllProductsDivofchangingpages">
    <div class="row inactiveBtnbackground Divofchangingpages">
        <div class="col-12">
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-6 m-auto">
                        <div class="row">
                            <div class="col-12 m-auto Divofchangingpagesheadtitle">
                                منتجات فئة {{ $Category->category_name }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 m-auto Divofchangingpagesnavlink">
                                <a href="{{ route('home') }}" class="Divofchangingpagesnavlink decorationnone">
                                    الرئيسية
                                </a>
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fas fa-chevron-left p-2"></i>
                                </button>
                                <a href="{{ route('EcommerceAllCategories') }}"
                                    class="Divofchangingpagesnavlink decorationnone">
                                    التصنيفات
                                </a>
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fas fa-chevron-left p-2"></i>
                                </button>
                                <a href="{{ route('CategoryProduct', $Category->category_id) }}"
                                    class="Divofchangingpagesnavlink decorationnone">
                                    {{ $Category->category_name }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="mobile_content_EcommerceAllProductsDivofchangingpages">
    <div class="row inactiveBtnbackground Divofchangingpages">
        <div class="col-12">
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-12 m-auto">
                        <div class="row">
                            <div class="col-12 m-auto Divofchangingpagesheadtitle">
                                منتجات فئة {{ $Category->category_name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
