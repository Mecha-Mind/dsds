@if (!$Ourcustomers->isEmpty())
    <div id="desktop_content_ourcustomer">
        <div class="row pt-5">
            <div class="container">
                <div class="row categoryheadlinetext d-flex align-items-center justify-content-end text-right">
                    <div class="col-6 d-flex align-items-center justify-content-start text-right">
                    </div>
                    <div class="col-6 d-flex align-items-center justify-content-end text-right Primarycolor">
                        عملاءنا
                    </div>
                </div>
                <div class="row d-flex align-items-center justify-content-center m-auto">
                    @foreach ($Ourcustomers as $Ourcustomer)
                        @if ($Ourcustomer->ourcustomer_image != null)
                            <div class="col-2 d-flex align-items-center justify-content-center p-3 m-auto">
                                <div class="ratio ratio-1x1 w-100">
                                    <img src="{{ url('/images/ourcustomerimages/' . $Ourcustomer->ourcustomer_image) }}"
                                        alt="Ourcustomer Image" class="img-fluid rounded-4 m-auto object-fit-cover">
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>


    <div id="mobile_content_ourcustomer">
        <div class="row">
            <div class="col-12 p-3 m-auto">
                <div class="row categorytitlehomepagemobile d-flex align-items-center justify-content-end text-right">
                    <div class="col-6 d-flex align-items-center justify-content-start text-right">
                    </div>
                    <div class="col-6 d-flex align-items-center justify-content-end text-right Primarycolor">
                        عملاءنا
                    </div>
                </div>
                <div class="row d-flex align-items-center justify-content-center m-auto">
                    @foreach ($Ourcustomers as $Ourcustomer)
                        @if ($Ourcustomer->ourcustomer_image != null)
                            <div class="col-6 d-flex align-items-center justify-content-center p-3 m-auto">
                                <div class="ratio ratio-1x1 w-100">
                                    <img src="{{ url('/images/ourcustomerimages/' . $Ourcustomer->ourcustomer_image) }}"
                                        alt="Ourcustomer Image" class="img-fluid rounded-4 m-auto object-fit-cover">
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
