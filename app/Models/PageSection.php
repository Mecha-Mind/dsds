<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $fillable = [
        'page_id',
        'title',
        'description',
        'image',
        'order',
        'type', // hero, content, features, stats, gallery, etc.
        'data', // JSON for additional data
    ];

    protected $casts = [
        'data' => 'json',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
