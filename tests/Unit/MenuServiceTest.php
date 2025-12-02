<?php

use App\Models\MenuItem;
use App\Services\MenuService;
use Illuminate\Support\Facades\Cache;

test('menu service retrieves menu items by location', function () {
    MenuItem::factory()->count(3)->create(['menu_location' => 'header', 'is_active' => true]);
    MenuItem::factory()->count(2)->create(['menu_location' => 'footer', 'is_active' => true]);

    $menuService = new MenuService();
    $headerMenu = $menuService->getMenu('header', false);

    expect($headerMenu)->toHaveCount(3);
});

test('menu service only retrieves active items', function () {
    MenuItem::factory()->create(['menu_location' => 'header', 'is_active' => true]);
    MenuItem::factory()->create(['menu_location' => 'header', 'is_active' => false]);

    $menuService = new MenuService();
    $menu = $menuService->getMenu('header', false);

    expect($menu)->toHaveCount(1);
});

test('menu service only retrieves root items', function () {
    $parent = MenuItem::factory()->create(['menu_location' => 'header', 'is_active' => true]);
    MenuItem::factory()->create([
        'menu_location' => 'header',
        'parent_id' => $parent->id,
        'is_active' => true,
    ]);

    $menuService = new MenuService();
    $menu = $menuService->getMenu('header', false);

    expect($menu)->toHaveCount(1); // Only root item
    expect($menu->first()->children)->toHaveCount(1); // With one child
});

test('menu service caches results', function () {
    MenuItem::factory()->create(['menu_location' => 'header', 'is_active' => true]);

    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(collect());

    $menuService = new MenuService();
    $menuService->getMenu('header', true);
});

test('menu service clears cache for specific location', function () {
    $menuService = new MenuService();

    Cache::shouldReceive('forget')
        ->once()
        ->with('menu_header');

    $menuService->clearCache('header');
});

test('menu service clears all caches', function () {
    $menuService = new MenuService();

    Cache::shouldReceive('forget')
        ->with('menu_header');
    Cache::shouldReceive('forget')
        ->with('menu_footer');

    $menuService->clearCache();
});

test('menu service builds hierarchical tree', function () {
    $parent = MenuItem::factory()->create([
        'menu_location' => 'header',
        'is_active' => true,
        'label' => 'Parent',
    ]);

    MenuItem::factory()->create([
        'menu_location' => 'header',
        'parent_id' => $parent->id,
        'is_active' => true,
        'label' => 'Child',
    ]);

    $menuService = new MenuService();
    $menu = $menuService->getMenu('header', false);
    $tree = $menuService->buildTree($menu);

    expect($tree)->toHaveCount(1);
    expect($tree[0]['label'])->toBe('Parent');
    expect($tree[0]['children'])->toHaveCount(1);
    expect($tree[0]['children'][0]['label'])->toBe('Child');
});

