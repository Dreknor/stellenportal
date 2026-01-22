<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Page::with(['creator', 'updater']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'draft') {
                $query->draft();
            }
        }

        $pages = $query->latest()->paginate(20)->withQueryString();

        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePageRequest $request)
    {
        $validated = $request->validated();

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Set published_at if publishing now
        if (!empty($validated['is_published']) && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $page = Page::create($validated);

        // Check if user wants to go directly to page builder
        if ($request->input('action') === 'save_and_builder') {
            return redirect()
                ->route('cms.pages.blocks.index', $page)
                ->with('success', 'Seite erfolgreich erstellt. Sie können jetzt Content Blocks hinzufügen.');
        }

        return redirect()
            ->route('cms.pages.edit', $page)
            ->with('success', 'Seite erfolgreich erstellt.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page)
    {
        $page->load(['creator', 'updater', 'images', 'menuItems']);

        return view('admin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $validated = $request->validated();

        // Set published_at if publishing now and it wasn't set before
        if (!empty($validated['is_published']) && empty($page->published_at) && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $validated['updated_by'] = Auth::id();

        $page->update($validated);

        return redirect()
            ->route('cms.pages.edit', $page)
            ->with('success', 'Seite erfolgreich aktualisiert.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()
            ->route('cms.pages.index')
            ->with('success', 'Seite erfolgreich gelöscht.');
    }

    /**
     * Publish the specified page.
     */
    public function publish(Page $page)
    {
        $page->publish();

        return redirect()
            ->back()
            ->with('success', 'Seite erfolgreich veröffentlicht.');
    }

    /**
     * Unpublish the specified page.
     */
    public function unpublish(Page $page)
    {
        $page->unpublish();

        return redirect()
            ->back()
            ->with('success', 'Veröffentlichung der Seite rückgängig gemacht.');
    }

    /**
     * Duplicate the specified page.
     */
    public function duplicate(Page $page)
    {
        // Create a new page with the same data
        $newPage = $page->replicate();

        // Modify title and slug for the duplicate
        $newPage->title = $page->title . ' (Kopie)';
        $newPage->slug = null; // Will be auto-generated
        $newPage->is_published = false;
        $newPage->published_at = null;
        $newPage->created_by = Auth::id();
        $newPage->updated_by = Auth::id();

        $newPage->save();

        // Copy images if any
        foreach ($page->images as $image) {
            $newImage = $image->replicate();
            $newImage->page_id = $newPage->id;
            $newImage->save();
        }

        return redirect()
            ->route('cms.pages.edit', $newPage)
            ->with('success', 'Seite erfolgreich dupliziert.');
    }

    /**
     * Show preview of the page (even if unpublished).
     */
    public function preview(Page $page)
    {
        $page->load([
            'images',
            'contentBlocks' => function($query) {
                $query->where('is_visible', true)
                      ->whereNull('parent_id')
                      ->orderBy('order', 'asc');
            },
            'contentBlocks.children' => function($query) {
                $query->where('is_visible', true)->orderBy('order', 'asc');
            }
        ]);

        return view('public.pages.show', compact('page'));
    }
}
