<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'menu_location',
        'parent_id',
        'page_id',
        'label',
        'url',
        'target',
        'order',
        'is_active',
        'css_class',
        'icon',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Get the parent menu item.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * Get all child menu items.
     */
    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get the page associated with this menu item.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Scope a query to only include active menu items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by menu location.
     */
    public function scopeByLocation($query, string $location)
    {
        return $query->where('menu_location', $location);
    }

    /**
     * Scope a query to order menu items.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope a query to only include root menu items (no parent).
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the URL for this menu item.
     * Returns the page URL if linked to a page, otherwise the custom URL.
     */
    public function getHrefAttribute(): string
    {
        if ($this->page_id && $this->page) {
            return route('pages.show', $this->page->slug);
        }

        return $this->url ?? '#';
    }

    /**
     * Get all descendants (children, grandchildren, etc.).
     */
    public function getDescendants(): array
    {
        $descendants = [];

        foreach ($this->children as $child) {
            $descendants[] = $child;
            $descendants = array_merge($descendants, $child->getDescendants());
        }

        return $descendants;
    }

    /**
     * Get all ancestors (parent, grandparent, etc.).
     */
    public function getAncestors(): array
    {
        $ancestors = [];
        $current = $this->parent;

        while ($current) {
            $ancestors[] = $current;
            $current = $current->parent;
        }

        return array_reverse($ancestors);
    }

    /**
     * Check if this menu item is a descendant of the given item.
     */
    public function isDescendantOf(MenuItem $item): bool
    {
        $parent = $this->parent;

        while ($parent) {
            if ($parent->id === $item->id) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }

    /**
     * Get the depth level of this menu item (0 for root items).
     */
    public function getDepthAttribute(): int
    {
        return count($this->getAncestors());
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Prevent circular references
        static::saving(function ($menuItem) {
            if ($menuItem->parent_id) {
                $parent = MenuItem::find($menuItem->parent_id);
                if ($parent && ($parent->id === $menuItem->id || $parent->isDescendantOf($menuItem))) {
                    throw new \Exception('Cannot create circular menu item reference.');
                }
            }
        });
    }
}
