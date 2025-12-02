<?php

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    /**
     * Get menu items for a specific location.
     *
     * @param string $location
     * @param bool $useCache
     * @return Collection
     */
    public function getMenu(string $location = 'header', bool $useCache = true): Collection
    {
        $cacheKey = "menu_{$location}";

        if ($useCache) {
            return Cache::remember($cacheKey, now()->addDay(), function () use ($location) {
                return $this->fetchMenu($location);
            });
        }

        return $this->fetchMenu($location);
    }

    /**
     * Fetch menu items from database.
     *
     * @param string $location
     * @return Collection
     */
    protected function fetchMenu(string $location): Collection
    {
        return MenuItem::with(['page', 'children' => function ($query) {
                $query->active()->ordered()->with(['page', 'children' => function ($q) {
                    $q->active()->ordered()->with('page');
                }]);
            }])
            ->byLocation($location)
            ->active()
            ->roots()
            ->ordered()
            ->get();
    }

    /**
     * Clear menu cache for a specific location or all locations.
     *
     * @param string|null $location
     * @return void
     */
    public function clearCache(?string $location = null): void
    {
        if ($location) {
            Cache::forget("menu_{$location}");
        } else {
            // Clear all menu caches
            Cache::forget('menu_header');
            Cache::forget('menu_footer');
        }
    }

    /**
     * Build a hierarchical menu tree.
     *
     * @param Collection $items
     * @return array
     */
    public function buildTree(Collection $items): array
    {
        return $items->map(function ($item) {
            return $this->buildTreeItem($item);
        })->toArray();
    }

    /**
     * Build a single menu tree item with its children.
     *
     * @param MenuItem $item
     * @return array
     */
    protected function buildTreeItem(MenuItem $item): array
    {
        $data = [
            'id' => $item->id,
            'label' => $item->label,
            'href' => $item->href,
            'target' => $item->target,
            'css_class' => $item->css_class,
            'icon' => $item->icon,
            'children' => [],
        ];

        if ($item->children && $item->children->count() > 0) {
            $data['children'] = $item->children->map(function ($child) {
                return $this->buildTreeItem($child);
            })->toArray();
        }

        return $data;
    }
}

