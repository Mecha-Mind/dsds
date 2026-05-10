<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $primaryKey = 'category_id';
    protected $fillable = ['category_name', 'category_slug', 'category_displaystatus', 'category_appearonhomepage', 'category_image'];

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }

    public function getRouteKeyName()
    {
        return 'category_slug';
    }

    /**
     * الحصول على الـ slug - إنشاؤه تلقائياً إذا كان فارغاً
     */
    public function getCategorySlugAttribute($value)
    {
        return $value ?: Str::slug($this->category_name);
    }

    /**
     * Scope للبحث بـ slug أو ID
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('category_slug', $slug)
                     ->orWhere('category_id', $slug);
    }

    /**
     * حل Route Model Binding - البحث عن category بـ slug
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('category_slug', $value)
                    ->orWhere('category_id', $value)
                    ->firstOrFail();
    }
}