<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'meta_title',
        'meta_description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * الأقسام المتعلقة بالصفحة
     */
    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('order');
    }

    /**
     * الحصول على الصفحة بـ slug
     */
    public static function findBySlug($slug)
    {
        return self::where('slug', $slug)->where('is_active', true)->firstOrFail();
    }
}
