<?php

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $adminRole->givePermissionTo('admin manage menus');

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
});

test('admin can view menus index', function () {
    MenuItem::factory()->count(3)->create(['menu_location' => 'header']);

    $response = $this->actingAs($this->admin)->get(route('admin.menus.index'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.menus.index');
    $response->assertViewHas('menuItems');
});

test('admin can create menu item with page', function () {
    $page = Page::factory()->published()->create();

    $response = $this->actingAs($this->admin)->post(route('admin.menus.store'), [
        'menu_location' => 'header',
        'label' => 'Test Menu',
        'page_id' => $page->id,
        'target' => '_self',
        'is_active' => true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('menu_items', [
        'label' => 'Test Menu',
        'page_id' => $page->id,
    ]);
});

test('admin can create menu item with url', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.menus.store'), [
        'menu_location' => 'header',
        'label' => 'External Link',
        'url' => 'https://example.com',
        'target' => '_blank',
        'is_active' => true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('menu_items', [
        'label' => 'External Link',
        'url' => 'https://example.com',
    ]);
});

test('admin can create child menu item', function () {
    $parent = MenuItem::factory()->create(['menu_location' => 'header']);

    $response = $this->actingAs($this->admin)->post(route('admin.menus.store'), [
        'menu_location' => 'header',
        'parent_id' => $parent->id,
        'label' => 'Sub Menu',
        'url' => '/test',
        'target' => '_self',
        'is_active' => true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('menu_items', [
        'label' => 'Sub Menu',
        'parent_id' => $parent->id,
    ]);
});

test('admin can update menu item', function () {
    $menuItem = MenuItem::factory()->create(['label' => 'Old Label']);

    $response = $this->actingAs($this->admin)->put(route('admin.menus.update', $menuItem), [
        'menu_location' => $menuItem->menu_location,
        'label' => 'Updated Label',
        'url' => '/updated',
        'target' => '_self',
        'is_active' => true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('menu_items', [
        'id' => $menuItem->id,
        'label' => 'Updated Label',
    ]);
});

test('admin can delete menu item', function () {
    $menuItem = MenuItem::factory()->create();

    $response = $this->actingAs($this->admin)->delete(route('admin.menus.destroy', $menuItem));

    $response->assertRedirect();
    $this->assertDatabaseMissing('menu_items', ['id' => $menuItem->id]);
});

test('menu items can be filtered by location', function () {
    MenuItem::factory()->count(2)->create(['menu_location' => 'header']);
    MenuItem::factory()->count(3)->create(['menu_location' => 'footer']);

    $response = $this->actingAs($this->admin)->get(route('admin.menus.index', ['location' => 'header']));

    $response->assertStatus(200);
});

test('non-admin cannot access menus', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.menus.index'));

    $response->assertStatus(403);
});

