<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'parent_id',
        'type',
        'content',
        'settings',
        'background_color',
        'order',
        'is_visible',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_visible' => 'boolean',
    ];

    /**
     * Get the page that owns the content block.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the parent block (for nested blocks).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ContentBlock::class, 'parent_id');
    }

    /**
     * Get child blocks (blocks nested within this block).
     */
    public function children()
    {
        return $this->hasMany(ContentBlock::class, 'parent_id')->orderBy('order');
    }

    /**
     * Scope for top-level blocks (no parent).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for visible blocks.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope for ordered blocks.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get available block types.
     */
    public static function getTypes(): array
    {
        return [
            'text' => 'Text Block',
            'heading' => 'Überschrift',
            'image' => 'Bild',
            'html' => 'HTML/Code',
            'quote' => 'Zitat',
            'button' => 'Button/CTA',
            'divider' => 'Trennlinie',
            'row' => 'Reihe/Container',
            'columns' => 'Spalten-Layout',
            'card' => 'Card',
            'card_image' => 'Card mit Header-Bild',
        ];
    }
}

