<?php

namespace App\Observers;

use App\Models\MenuItem;
use App\Services\MenuService;

class MenuItemObserver
{
    /**
     * Handle the MenuItem "created" event.
     */
    public function created(MenuItem $menuItem): void
    {
        $this->clearMenuCache($menuItem);
    }

    /**
     * Handle the MenuItem "updated" event.
     */
    public function updated(MenuItem $menuItem): void
    {
        $this->clearMenuCache($menuItem);
    }

    /**
     * Handle the MenuItem "deleted" event.
     */
    public function deleted(MenuItem $menuItem): void
    {
        $this->clearMenuCache($menuItem);
    }

    /**
     * Clear menu cache for the affected location.
     */
    protected function clearMenuCache(MenuItem $menuItem): void
    {
        $menuService = app(MenuService::class);
        $menuService->clearCache($menuItem->menu_location);
    }
}

