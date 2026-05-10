@if (!$PartnerCompanies->isEmpty())
    <div id="desktop_content_partnercompany">
        <div class="row inactiveBtnbackground">
            <div class="container">
                <div class="row d-flex align-items-center justify-content-center m-auto inactiveBtnbackground">
                    <div class="scroll-container inactiveBtnbackground">
                        <div class="scroll-track" id="scrollTrackDesktop">
                            @foreach ($PartnerCompanies as $PartnerCompany)
                                @if ($PartnerCompany->maintenancecompany_image != null)
                                    <a href="{{ route('CompanyProduct', $PartnerCompany->maintenancecompany_id) }}"
                                        class="text-decoration-none">
                                        <div
                                            class="col-2 d-flex align-items-center justify-content-center h-100 p-3 m-auto">
                                            <div class="scroll-item">
                                                <img class="compnayimage"
                                                    src="{{ url('/images/partnercompany/' . $PartnerCompany->maintenancecompany_image) }}"
                                                    alt="{{ $PartnerCompany->maintenancecompany_title }}">
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <div id="mobile_content_partnercompany">
        <div class="row inactiveBtnbackground">
            <div class="col-12 p-3 m-auto">
                <div
                    class="row d-flex align-items-center justify-content-center m-auto inactiveBtnbackground partnercompanymobile">
                    <div class="scroll-container inactiveBtnbackground">
                        <div class="scroll-track" id="scrollTrackMobile">
                            @foreach ($PartnerCompanies as $PartnerCompany)
                                @if ($PartnerCompany->maintenancecompany_image != null)
                                    <a href="{{ route('CompanyProduct', $PartnerCompany->maintenancecompany_id) }}"
                                        class="text-decoration-none">
                                        <div
                                            class="col-2 d-flex align-items-center justify-content-center h-100  m-auto">
                                            <div class="scroll-item">

                                                <img class="compnayimagemobile"
                                                    src="{{ url('/images/partnercompany/' . $PartnerCompany->maintenancecompany_image) }}"
                                                    alt="{{ $PartnerCompany->maintenancecompany_title }}">
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
