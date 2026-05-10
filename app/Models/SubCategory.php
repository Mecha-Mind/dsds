<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubCategory extends Model
{
    protected $table = 'subcategories';     // ← مهم جداً
    protected $primaryKey = 'subcategory_id';
    
    protected $fillable = [
        'subcategory_name', 
        'subcategory_slug',
        'subcategory_displaystatus', 
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function getRouteKeyName()
    {
        return 'subcategory_slug';
    }

    /**
     * الحصول على الـ slug - إنشاؤه تلقائياً إذا كان فارغاً
     */
    public function getSubcategorySlugAttribute($value)
    {
        return $value ?: Str::slug($this->subcategory_name);
    }

    /**
     * Scope للبحث بـ slug أو ID
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('subcategory_slug', $slug)
                     ->orWhere('subcategory_id', $slug);
    }

    /**
     * حل Route Model Binding - البحث عن subcategory بـ slug
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('subcategory_slug', $value)
                    ->orWhere('subcategory_id', $value)
                    ->firstOrFail();
    }
}