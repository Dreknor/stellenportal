<?php

use App\Models\Page;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create admin role with permissions
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $adminRole->givePermissionTo([
        'admin view pages',
        'admin create pages',
        'admin edit pages',
        'admin delete pages',
        'admin publish pages',
    ]);

    // Create admin user
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
});

test('admin can view pages index', function () {
    Page::factory()->count(3)->create();

    $response = $this->actingAs($this->admin)->get(route('admin.pages.index'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.pages.index');
    $response->assertViewHas('pages');
});

test('admin can view create page form', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.pages.create'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.pages.create');
});

test('admin can store new page', function () {
    $pageData = [
        'title' => 'Test Page',
        'content' => 'Test content',
        'is_published' => false,
    ];

    $response = $this->actingAs($this->admin)->post(route('admin.pages.store'), $pageData);

    $response->assertRedirect();
    $this->assertDatabaseHas('pages', [
        'title' => 'Test Page',
        'slug' => 'test-page',
    ]);
});

test('admin can view single page', function () {
    $page = Page::factory()->create();

    $response = $this->actingAs($this->admin)->get(route('admin.pages.show', $page));

    $response->assertStatus(200);
    $response->assertViewIs('admin.pages.show');
    $response->assertViewHas('page');
});

test('admin can view edit page form', function () {
    $page = Page::factory()->create();

    $response = $this->actingAs($this->admin)->get(route('admin.pages.edit', $page));

    $response->assertStatus(200);
    $response->assertViewIs('admin.pages.edit');
    $response->assertViewHas('page');
});

test('admin can update page', function () {
    $page = Page::factory()->create(['title' => 'Old Title']);

    $response = $this->actingAs($this->admin)->put(route('admin.pages.update', $page), [
        'title' => 'Updated Title',
        'slug' => $page->slug,
        'content' => 'Updated content',
        'is_published' => false,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('pages', [
        'id' => $page->id,
        'title' => 'Updated Title',
    ]);
});

test('admin can delete page', function () {
    $page = Page::factory()->create();

    $response = $this->actingAs($this->admin)->delete(route('admin.pages.destroy', $page));

    $response->assertRedirect(route('admin.pages.index'));
    $this->assertSoftDeleted('pages', ['id' => $page->id]);
});

test('admin can publish page', function () {
    $page = Page::factory()->draft()->create();

    $response = $this->actingAs($this->admin)->post(route('admin.pages.publish', $page));

    $response->assertRedirect();
    $page->refresh();
    expect($page->is_published)->toBeTrue();
    expect($page->published_at)->not->toBeNull();
});

test('admin can unpublish page', function () {
    $page = Page::factory()->published()->create();

    $response = $this->actingAs($this->admin)->post(route('admin.pages.unpublish', $page));

    $response->assertRedirect();
    $page->refresh();
    expect($page->is_published)->toBeFalse();
});

test('non-admin cannot access pages', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.pages.index'));

    $response->assertStatus(403);
});

test('slug is auto-generated if not provided', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.pages.store'), [
        'title' => 'My Test Page',
        'content' => 'Content',
        'is_published' => false,
    ]);

    $this->assertDatabaseHas('pages', [
        'title' => 'My Test Page',
        'slug' => 'my-test-page',
    ]);
});

test('pages can be filtered by status', function () {
    Page::factory()->published()->count(2)->create();
    Page::factory()->draft()->count(3)->create();

    $response = $this->actingAs($this->admin)->get(route('admin.pages.index', ['status' => 'published']));

    $response->assertStatus(200);
});

test('pages can be searched', function () {
    Page::factory()->create(['title' => 'Searchable Page']);
    Page::factory()->create(['title' => 'Other Page']);

    $response = $this->actingAs($this->admin)->get(route('admin.pages.index', ['search' => 'Searchable']));

    $response->assertStatus(200);
});
