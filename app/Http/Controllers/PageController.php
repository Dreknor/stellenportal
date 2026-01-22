<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the specified page.
     */
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)
            ->published()
            ->with([
                'images',
                'contentBlocks' => function($query) {
                    $query->where('is_visible', true)
                          ->whereNull('parent_id')
                          ->orderBy('order', 'asc');
                },
                'contentBlocks.children' => function($query) {
                    $query->where('is_visible', true)->orderBy('order', 'asc');
                }
            ])
            ->firstOrFail();

        return view('public.pages.show', compact('page'));
    }
}

