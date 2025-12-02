<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display the menu builder interface.
     */
    public function index(Request $request)
    {
        $location = $request->get('location', 'header');

        $menuItems = MenuItem::with(['page', 'children.page', 'children.children.page'])
            ->byLocation($location)
            ->roots()
            ->ordered()
            ->get();

        $pages = Page::published()->orderBy('title')->get();

        $locations = [
            'header' => 'Header Navigation',
            'footer' => 'Footer Navigation',
        ];

        return view('admin.menus.index', compact('menuItems', 'pages', 'location', 'locations'));
    }

    /**
     * Store a new menu item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_location' => ['required', 'string', 'in:header,footer'],
            'parent_id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'label' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],
            'target' => ['required', 'in:_self,_blank'],
            'css_class' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        // Ensure either page_id or url is provided
        if (empty($validated['page_id']) && empty($validated['url'])) {
            return back()->withErrors(['url' => 'Entweder eine Seite oder eine URL muss angegeben werden.']);
        }

        // Get the max order for this location and parent
        $maxOrder = MenuItem::byLocation($validated['menu_location'])
            ->where('parent_id', $validated['parent_id'] ?? null)
            ->max('order') ?? -1;

        $validated['order'] = $maxOrder + 1;

        $menuItem = MenuItem::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'menuItem' => $menuItem->load('page'),
                'message' => 'Menü-Item erfolgreich erstellt.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Menü-Item erfolgreich erstellt.');
    }

    /**
     * Update the specified menu item.
     */
    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'menu_location' => ['required', 'string', 'in:header,footer'],
            'parent_id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'label' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],
            'target' => ['required', 'in:_self,_blank'],
            'css_class' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        // Ensure either page_id or url is provided
        if (empty($validated['page_id']) && empty($validated['url'])) {
            return back()->withErrors(['url' => 'Entweder eine Seite oder eine URL muss angegeben werden.']);
        }

        // Prevent circular references
        if (!empty($validated['parent_id']) && $validated['parent_id'] == $menuItem->id) {
            return back()->withErrors(['parent_id' => 'Ein Menü-Item kann nicht sein eigenes Elternelement sein.']);
        }

        $menuItem->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'menuItem' => $menuItem->load('page'),
                'message' => 'Menü-Item erfolgreich aktualisiert.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Menü-Item erfolgreich aktualisiert.');
    }

    /**
     * Remove the specified menu item.
     */
    public function destroy(Request $request, MenuItem $menuItem)
    {
        $menuItem->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Menü-Item erfolgreich gelöscht.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Menü-Item erfolgreich gelöscht.');
    }

    /**
     * Reorder menu items and update hierarchy.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.parent_id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'items.*.order' => ['required', 'integer'],
        ]);

        foreach ($validated['items'] as $item) {
            MenuItem::where('id', $item['id'])->update([
                'parent_id' => $item['parent_id'] ?? null,
                'order' => $item['order'],
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Menü-Reihenfolge erfolgreich aktualisiert.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Menü-Reihenfolge erfolgreich aktualisiert.');
    }
}
