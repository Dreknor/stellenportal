<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FooterSetting extends Model
{
    protected $fillable = [
        'logo_path',
        'content',
        'links',
        'background_color',
        'text_color',
        'link_color',
        'is_active',
    ];

    protected $casts = [
        'links' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the active footer setting
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Get the logo URL
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo_path) {
            return Storage::url($this->logo_path);
        }
        return null;
    }

    /**
     * Delete the logo file
     */
    public function deleteLogo()
    {
        if ($this->logo_path && Storage::exists($this->logo_path)) {
            Storage::delete($this->logo_path);
        }
    }
}

