<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        // حفظ الرسالة في ملف log
        \Log::info('Contact Form Submission', $validated);

        // يمكنك إضافة حفظ في قاعدة البيانات لاحقاً
        // ContactMessage::create($validated);

        return back()->with('success', 'شكراً! تم استلام رسالتك. سنرد عليك قريباً.');
    }
}
