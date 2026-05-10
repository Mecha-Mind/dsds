<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\PageService;
use Illuminate\Http\Request;

class PageController extends BaseController
{
    private PageService $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * عرض صفحة ثابتة
     */
    public function show($slug)
    {
        $page = $this->pageService->getPageBySlug($slug);

        if (!$page) {
            abort(404);
        }

        $data = $this->getSharedData();
        $data['page'] = $page;

        return view('pages.dynamic', $data);
    }

    /**
     * عرض صفحة التواصل
     */
    public function contact()
    {
        $page = $this->pageService->getPageBySlug('contact') ?? [
            'title' => 'تواصل معنا',
            'description' => 'نحن هنا للإجابة على استفساراتك',
        ];

        $data = $this->getSharedData();
        $data['page'] = $page;

        return view('pages.contact', $data);
    }

    /**
     * حفظ رسالة تواصل
     */
    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        $validated['ip_address'] = $request->ip();

        ContactMessage::create($validated);

        // تسجيل في السجلات
        \Log::info('Contact Form Submission', $validated);

        return back()->with('success', 'شكراً! تم استلام رسالتك. سنرد عليك قريباً.');
    }
}
