@php
    $Page_title = $ecommerceSharedData['branch']->branch_name . ' - الصفحة الشخصية ';
    $description = 'الصفحة الشخصية لفرع ' . $ecommerceSharedData['branch']->branch_name . '، تعرف على معلومات الفرع وأحدث الأخبار والخدمات.';
    $keywords = $ecommerceSharedData['branch']->branch_name . ', فرع, صفحة شخصية, خدمات, معلومات, اتصل بنا';
    $og_title = $ecommerceSharedData['branch']->branch_name . ' - الصفحة الشخصية';
    $og_description = 'تصفح الصفحة الشخصية لفرع ' . $ecommerceSharedData['branch']->branch_name . ' وتعرف على جميع التفاصيل والخدمات المتاحة.';
    $og_image = url('/images/brancheslogo/' . $ecommerceSharedData['branch']->branch_image);
    $og_type = 'website';
    
@endphp

@extends('layouts.app')


@section('title', $Page_title)
@section('description', $description)
@section('content')

    @if ($Customer)
        <div id="desktop_content_ShoppingCart">

            <div class="container pb-5">
                <div class="row g-4">
                    <div class="col-md-9">
                        <div class="card p-4 text-center">
                            <h5 class="text-end fw-bold footerfirstheadline">
                                معلوماتي الشخصية
                            </h5>
                            <form action="{{ route('UserPersonalPageIDPost', $Customer->customer_id) }}" method="POST"
                                class="d-inline">
                                @csrf
                                {{-- الاسم --}}
                                <div class="row text-end mb-2 align-items-center">
                                    <div class="col-9 text-end">
                                        <div class="input-group">
                                            <input type="text" name="customer_name"
                                                class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                                value="{{ $Customer->customer_name }}"
                                                style="background: transparent; box-shadow: none; font-size: 14px;">
                                            <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                        </div>
                                    </div>
                                    <div class="col-3 text-end">
                                        <p class="footerfirstdescription">: الاسم</p>
                                    </div>
                                </div>

                                {{-- رقم الهاتف --}}
                                <div class="row text-end mb-2 align-items-center">
                                    <div class="col-9 text-end">
                                        <div class="input-group">
                                            <input type="text" name="customer_phone"
                                                class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                                value="{{ $Customer->customer_phone }}" pattern="01[0-9]{9}" maxlength="11"
                                                inputmode="numeric" title="رقم الهاتف يجب أن يبدأ بـ 01 ويتكون من 11 رقم"
                                                style="background: transparent; box-shadow: none; font-size: 14px;">
                                            <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                        </div>
                                    </div>
                                    <div class="col-3 text-end">
                                        <p class="footerfirstdescription">: رقم الهاتف</p>
                                    </div>
                                </div>

                                {{-- رقم هاتف ثاني --}}
                                <div class="row text-end mb-2 align-items-center">
                                    <div class="col-9 text-end">
                                        <div class="input-group">
                                            <input type="text" name="customer_phone2"
                                                class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                                value="{{ $Customer->customer_phone2 }}" pattern="01[0-9]{9}" maxlength="11"
                                                inputmode="numeric" title="رقم الهاتف يجب أن يبدأ بـ 01 ويتكون من 11 رقم"
                                                style="background: transparent; box-shadow: none; font-size: 14px;">
                                            <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                        </div>
                                    </div>
                                    <div class="col-3 text-end">
                                        <p class="footerfirstdescription">: رقم هاتف ثاني</p>
                                    </div>
                                </div>


                                {{-- البريد الإلكتروني --}}
                                {{-- البريد الإلكتروني --}}
                                <div class="row text-end mb-2 align-items-center">
                                    <div class="col-9 text-end">
                                        <div class="input-group">
                                            <input type="email" name="customer_email"
                                                class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                                value="{{ $Customer->customer_email }}"
                                                title="من فضلك أدخل بريدًا إلكترونيًا صحيحًا مثل example@email.com"
                                                style="background: transparent; box-shadow: none; font-size: 14px;">
                                            <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                        </div>
                                    </div>
                                    <div class="col-3 text-end">
                                        <p class="footerfirstdescription">: البريد الإلكتروني</p>
                                    </div>
                                </div>


                                {{-- العنوان --}}
                                <div class="row text-end mb-2 align-items-center">
                                    <div class="col-9 text-end">
                                        <div class="input-group">
                                            <input type="text" name="customer_address"
                                                class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                                value="{{ $Customer->customer_address }}"
                                                style="background: transparent; box-shadow: none; font-size: 14px;">
                                            <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                        </div>
                                    </div>
                                    <div class="col-3 text-end">
                                        <p class="footerfirstdescription">: العنوان</p>
                                    </div>
                                </div>

                                {{-- رقم الشات --}}
                                <div class="row text-end mb-2 align-items-center">
                                    <div class="col-9 text-end">
                                        <div class="input-group">
                                            <input type="text" name="customer_telegramchatid"
                                                class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                                value="{{ $Customer->customer_telegramchatid }}"
                                                style="background: transparent; box-shadow: none; font-size: 14px;">
                                            <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                        </div>
                                    </div>
                                    <div class="col-3 text-end">
                                        <p class="footerfirstdescription">: رقم الشات</p>
                                    </div>
                                </div>

                                {{-- كلمة السر --}}
                                <div class="row text-end mb-2 align-items-center">
                                    <div class="col-9 text-end">
                                        <div class="input-group">
                                            <input type="customer_password" name="customer_password"
                                                class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                                placeholder="اتركها فارغة إن لم ترغب بالتغيير"
                                                style="background: transparent; box-shadow: none; font-size: 14px;">
                                            <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                        </div>
                                    </div>
                                    <div class="col-3 text-end">
                                        <p class="footerfirstdescription">: كلمة السر</p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card p-0 footerfirstdescription">
                            <a href="{{ route('UserPersonalPage') }}"
                                class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                                معلوماتي الشخصية
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fas fa-user"></i></button>
                            </a>
                            <a href="{{ route('UserPersonalUnderRequstProducts') }}"
                                class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                                طلباتي
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fas fa-shopping-bag"></i></button>
                            </a>
                            <a href="{{ route('UserPersonalStatement') }}"
                                class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                                كشف حسابي
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fas fa-credit-card"></i></button>
                            </a>
                            <a href="{{ route('UserPersonalLogOut') }}"
                                class="UserPersonalPageLogoutLink text-right">تسجيل
                                الخروج</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="mobile_content_ShoppingCart">
            <div class="row g-4 pb-4">
                <div class="col-md-12">
                    <div class="card p-0 footerfirstdescription">
                        <a href="{{ route('UserPersonalPage') }}"
                            class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                            معلوماتي الشخصية
                            <button class="btn apponwer_systemprimarybtn">
                                <i class="fas fa-user"></i></button>
                        </a>
                        <a href="{{ route('UserPersonalUnderRequstProducts') }}"
                            class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                            طلباتي
                            <button class="btn apponwer_systemprimarybtn">
                                <i class="fas fa-shopping-bag"></i></button>
                        </a>
                        <a href="{{ route('UserPersonalStatement') }}"
                            class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                            كشف حسابي
                            <button class="btn apponwer_systemprimarybtn">
                                <i class="fas fa-credit-card"></i></button>
                        </a>
                        <a href="{{ route('UserPersonalLogOut') }}" class="UserPersonalPageLogoutLink text-right">تسجيل
                            الخروج</a>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card p-4 text-center">
                        <h5 class="text-end fw-bold footerfirstheadline">
                            معلوماتي الشخصية
                        </h5>
                        <form action="{{ route('UserPersonalPageIDPost', $Customer->customer_id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            {{-- الاسم --}}
                            <div class="row text-end mb-2 align-items-center">
                                <div class="col-8 text-end">
                                    <div class="input-group">
                                        <input type="text" name="customer_name"
                                            class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                            value="{{ $Customer->customer_name }}"
                                            style="background: transparent; box-shadow: none; font-size: 14px;">
                                        <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <p class="footerfirstdescription">: الاسم</p>
                                </div>
                            </div>

                            {{-- رقم الهاتف --}}
                            <div class="row text-end mb-2 align-items-center">
                                <div class="col-8 text-end">
                                    <div class="input-group">
                                        <input type="text" name="customer_phone"
                                            class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                            value="{{ $Customer->customer_phone }}" pattern="01[0-9]{9}" maxlength="11"
                                            inputmode="numeric" title="رقم الهاتف يجب أن يبدأ بـ 01 ويتكون من 11 رقم"
                                            style="background: transparent; box-shadow: none; font-size: 14px;">
                                        <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <p class="footerfirstdescription">: رقم الهاتف</p>
                                </div>
                            </div>

                            {{-- رقم هاتف ثاني --}}
                            <div class="row text-end mb-2 align-items-center">
                                <div class="col-8 text-end">
                                    <div class="input-group">
                                        <input type="text" name="customer_phone2"
                                            class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                            value="{{ $Customer->customer_phone2 }}" pattern="01[0-9]{9}" maxlength="11"
                                            inputmode="numeric" title="رقم الهاتف يجب أن يبدأ بـ 01 ويتكون من 11 رقم"
                                            style="background: transparent; box-shadow: none; font-size: 14px;">
                                        <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <p class="footerfirstdescription">: رقم هاتف ثاني</p>
                                </div>
                            </div>

                            {{-- البريد الإلكتروني --}}
                            <div class="row text-end mb-2 align-items-center">
                                <div class="col-8 text-end">
                                    <div class="input-group">
                                        <input type="email" name="customer_email"
                                            class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                            value="{{ $Customer->customer_email }}"
                                            title="من فضلك أدخل بريدًا إلكترونيًا صحيحًا مثل example@email.com"
                                            style="background: transparent; box-shadow: none; font-size: 14px;">
                                        <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <p class="footerfirstdescription">: البريد الإلكتروني</p>
                                </div>
                            </div>


                            {{-- العنوان --}}
                            <div class="row text-end mb-2 align-items-center">
                                <div class="col-8 text-end">
                                    <div class="input-group">
                                        <input type="text" name="customer_address"
                                            class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                            value="{{ $Customer->customer_address }}"
                                            style="background: transparent; box-shadow: none; font-size: 14px;">
                                        <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <p class="footerfirstdescription">: العنوان</p>
                                </div>
                            </div>

                            {{-- رقم الشات --}}
                            <div class="row text-end mb-2 align-items-center">
                                <div class="col-8 text-end">
                                    <div class="input-group">
                                        <input type="text" name="customer_telegramchatid"
                                            class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                            value="{{ $Customer->customer_telegramchatid }}"
                                            style="background: transparent; box-shadow: none; font-size: 14px;">
                                        <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <p class="footerfirstdescription">: رقم الشات</p>
                                </div>
                            </div>

                            {{-- كلمة السر --}}
                            <div class="row text-end mb-2 align-items-center">
                                <div class="col-8 text-end">
                                    <div class="input-group">
                                        <input type="customer_password" name="customer_password"
                                            class="form-control form-control-sm border-0 footerfirstdescription text-end editable-input"
                                            placeholder="اتركها فارغة إن لم ترغب بالتغيير"
                                            style="background: transparent; box-shadow: none; font-size: 14px;">
                                        <button type="submit" class="btn btn-outline-success btn-sm">✅</button>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <p class="footerfirstdescription">: كلمة السر</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    @endif

@endsection