<?php

namespace App\Http\Controllers\Ecommerce\EcommerceContactUs;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\Category;
use App\Models\ContactUsMessage;
use App\Models\EcommerceProduct;
use App\Models\Employee;
use App\Models\LoginHistoriesCustomer;
use App\Models\MaintenanceCompany;
use App\Models\Product;
use App\Services\EcommerceSharedDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EcommerceContactUsController extends Controller
{
    public function EcommerceContactUs()
    {
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'تواصل معنا';

        $AboutUs = AboutUs::where('id', '1')->first();

        $Employees = Employee::where('employee_displaystatus', '1')
            ->whereNotNull('employee_image')
            ->get();


        return view('ecommerce.EcommerceContactUs.EcommerceContactUs', compact(
            'Employees',
            'AboutUs',
            'ecommerceSharedData',
        ));
    }

    public function CustomerContactUsMessages(Request $request)
    {

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'تواصل معنا';

        $customerPhone = session('customer_phone');
        // dd($customerPhone);

        if ($customerPhone  == null) {
            return redirect()
                ->route('CustomerLogin')
                ->withErrors([' يجب تسجيل الدخول اول حتي نتمكن من استلام الرسالة ']);
        }

        // dd($request->all());

        $validator = Validator::make($request->all(), [
            '_token' => 'required|string',
            '_method' => 'required|string|in:post',
            'message' => 'required|string|min:5|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $Customer = LoginHistoriesCustomer::where('loginhistorycustomer_phone', $customerPhone)->first();

        $message = $request->message;
        $now = date('Y-m-d H:i:s');

        // $CustomerMessages = ContactUsMessage::where('contactusmessage_customerphone', $customerPhone)
        //     ->where('contactusmessage_readed', '0')
        //     ->get();


        // if ($CustomerMessages->isNotEmpty()) {
        //     return redirect()
        //         ->route('EcommerceContactUs')
        //         ->withErrors([' يرجي الانتظار حتي يتم الرد علي الرسالة السابقة ']);
        // }

        $hasPendingMessages = ContactUsMessage::where('contactusmessage_customerphone', $customerPhone)
            ->where('contactusmessage_readed', '0')
            ->exists();

        if ($hasPendingMessages) {
            return redirect()
                ->route('EcommerceContactUs')
                ->withErrors(['يرجي الانتظار حتي يتم الرد علي الرسالة السابقة.']);
        }

        $contactusmessage_customername = $Customer->loginhistorycustomer_name;
        $contactusmessage_customerphone = $Customer->loginhistorycustomer_phone;
        $contactusmessage_customertelegramchatid = $Customer->loginhistorycustomer_telegramchatid;
        $contactusmessage_customeremail = $Customer->loginhistorycustomer_email;
        $contactusmessage_customerentertime = $Customer->loginhistorycustomer_entertime;
        $contactusmessage_customermessage = $message;
        $contactusmessage_customermessagetime = $now;

        $ContactUsMessage = new ContactUsMessage;
        $ContactUsMessage->contactusmessage_customername = $contactusmessage_customername;
        $ContactUsMessage->contactusmessage_customerphone = $contactusmessage_customerphone;
        $ContactUsMessage->contactusmessage_customertelegramchatid = $contactusmessage_customertelegramchatid;
        $ContactUsMessage->contactusmessage_customeremail = $contactusmessage_customeremail;
        $ContactUsMessage->contactusmessage_customerentertime = $contactusmessage_customerentertime;
        $ContactUsMessage->contactusmessage_customermessage = $contactusmessage_customermessage;
        $ContactUsMessage->contactusmessage_customermessagetime = $contactusmessage_customermessagetime;
        $ContactUsMessage->save();

        $AboutUs = AboutUs::where('id', '1')->first();

        $Employees = Employee::where('employee_displaystatus', '1')
            ->whereNotNull('employee_image')
            ->get();

        return view('ecommerce.EcommerceContactUs.EcommerceContactUsMessage', compact(
            'Employees',
            'AboutUs',
            'ecommerceSharedData',
        ));
    }
}
