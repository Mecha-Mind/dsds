<?php

namespace App\Services;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Support\Collection;

class PageService
{
    /**
     * الحصول على صفحة كاملة مع أقسامها
     */
    public function getPageBySlug(string $slug): ?array
    {
        $page = Page::findBySlug($slug);

        if (!$page) {
            return null;
        }

        $sections = $page->sections()
            ->orderBy('order')
            ->get()
            ->map(fn($section) => [
                'id' => $section->id,
                'title' => $section->title,
                'description' => $section->description,
                'image' => $section->image,
                'type' => $section->type,
                'data' => $section->data ?? [],
            ])
            ->toArray();

        return [
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'description' => $page->description,
            'content' => $page->content,
            'meta_title' => $page->meta_title ?: $page->title,
            'meta_description' => $page->meta_description ?: $page->description,
            'sections' => $sections,
        ];
    }

    /**
     * الحصول على جميع الصفحات النشطة
     */
    public function getAllPages(): Collection
    {
        return Page::where('is_active', true)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * إنشاء أو تحديث صفحة
     */
    public function savePage(array $data): Page
    {
        return Page::updateOrCreate(
            ['slug' => $data['slug']],
            [
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'content' => $data['content'] ?? null,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]
        );
    }

    /**
     * إضافة قسم لصفحة
     */
    public function addSection(int $pageId, array $data): PageSection
    {
        $order = PageSection::where('page_id', $pageId)->max('order') ?? 0;

        return PageSection::create([
            'page_id' => $pageId,
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'type' => $data['type'] ?? 'content',
            'data' => $data['data'] ?? null,
            'order' => $data['order'] ?? ($order + 1),
        ]);
    }

    /**
     * إزالة قسم
     */
    public function removeSection(int $sectionId): bool
    {
        return PageSection::destroy($sectionId) > 0;
    }
}
