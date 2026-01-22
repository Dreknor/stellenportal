<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use App\Models\Page;
use Illuminate\Http\Request;

class ContentBlockController extends Controller
{
    /**
     * Display content blocks for a page.
     */
    public function index(Page $page)
    {
        $page->load([
            'contentBlocks' => function($query) {
                $query->topLevel()->with('children')->orderBy('order', 'asc');
            },
            'images'
        ]);
        $blockTypes = ContentBlock::getTypes();
        $images = $page->images;

        return view('admin.pages.blocks.index', compact('page', 'blockTypes', 'images'));
    }

    /**
     * Store a new content block.
     */
    public function store(Request $request, Page $page)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'content' => 'nullable|string',
            'settings' => 'nullable|array',
            'order' => 'nullable|integer',
            'parent_id' => 'nullable|integer|exists:content_blocks,id',
            'background_color' => 'nullable|string|max:20',
        ]);

        $validated['page_id'] = $page->id;

        // Determine order based on parent
        if (isset($validated['parent_id'])) {
            $validated['order'] = $validated['order'] ?? ContentBlock::where('parent_id', $validated['parent_id'])->count();
        } else {
            $validated['order'] = $validated['order'] ?? $page->contentBlocks()->topLevel()->count();
        }

        $block = ContentBlock::create($validated);

        return redirect()
            ->route('cms.pages.blocks.index', $page)
            ->with('success', 'Block erfolgreich hinzugefügt.');
    }

    /**
     * Update a content block.
     */
    public function update(Request $request, Page $page, ContentBlock $block)
    {
        $validated = $request->validate([
            'content' => 'nullable|string',
            'settings' => 'nullable|array',
            'settings.*' => 'nullable',
            'is_visible' => 'nullable|boolean',
            'background_color' => 'nullable|string|max:20',
            'parent_id' => 'nullable|integer|exists:content_blocks,id',
        ]);

        $block->update($validated);

        return redirect()
            ->route('cms.pages.blocks.index', $page)
            ->with('success', 'Block erfolgreich aktualisiert.');
    }

    /**
     * Remove a content block.
     */
    public function destroy(Page $page, ContentBlock $block)
    {
        $block->delete();

        return redirect()
            ->route('cms.pages.blocks.index', $page)
            ->with('success', 'Block erfolgreich gelöscht.');
    }

    /**
     * Reorder content blocks.
     */
    public function reorder(Request $request, Page $page)
    {
        $validated = $request->validate([
            'blocks' => 'required|array',
            'blocks.*.id' => 'required|exists:content_blocks,id',
            'blocks.*.order' => 'required|integer',
            'blocks.*.parent_id' => 'nullable|integer|exists:content_blocks,id',
        ]);

        foreach ($validated['blocks'] as $blockData) {
            ContentBlock::where('id', $blockData['id'])
                ->update([
                    'order' => $blockData['order'],
                    'parent_id' => $blockData['parent_id'] ?? null,
                ]);
        }

        return response()->json(['success' => true]);
    }
}

