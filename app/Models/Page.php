<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class Page extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_published',
        'published_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from title if not provided
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });

        // Update slug if title changes and slug was auto-generated
        static::updating(function ($page) {
            if ($page->isDirty('title') && empty($page->getOriginal('slug'))) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    /**
     * Get the user who created this page.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this page.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all images for this page.
     */
    public function images(): HasMany
    {
        return $this->hasMany(PageImage::class)->orderBy('order');
    }

    /**
     * Get all menu items that link to this page.
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    /**
     * Get all content blocks for this page.
     */
    public function contentBlocks(): HasMany
    {
        return $this->hasMany(ContentBlock::class)->orderBy('order', 'asc');
    }

    /**
     * Scope a query to only include published pages.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include draft pages.
     */
    public function scopeDraft($query)
    {
        return $query->where('is_published', false)
            ->orWhereNull('published_at')
            ->orWhere('published_at', '>', now());
    }

    /**
     * Get an excerpt from the content.
     */
    public function getExcerptAttribute(): string
    {
        if (empty($this->content)) {
            return '';
        }

        return Str::limit(strip_tags($this->content), 200);
    }

    /**
     * Get the meta title or fall back to the title.
     */
    public function getMetaTitleAttribute($value): string
    {
        return $value ?? $this->title;
    }

    /**
     * Publish the page.
     */
    public function publish(): bool
    {
        $this->is_published = true;
        $this->published_at = $this->published_at ?? now();
        return $this->save();
    }

    /**
     * Unpublish the page.
     */
    public function unpublish(): bool
    {
        $this->is_published = false;
        return $this->save();
    }
}
