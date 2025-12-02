<?php

use App\Models\Page;
use App\Models\User;
use App\Models\PageImage;
use App\Models\MenuItem;

test('page can be created with required fields', function () {
    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => 'test-page',
        'content' => 'Test content',
    ]);

    expect($page->title)->toBe('Test Page')
        ->and($page->slug)->toBe('test-page')
        ->and($page->content)->toBe('Test content');
});

test('slug is auto-generated from title if not provided', function () {
    $page = Page::factory()->create([
        'title' => 'My Test Page',
        'slug' => null,
    ]);

    expect($page->slug)->toBe('my-test-page');
});

test('page has creator relationship', function () {
    $user = User::factory()->create();
    $page = Page::factory()->create(['created_by' => $user->id]);

    expect($page->creator)->toBeInstanceOf(User::class)
        ->and($page->creator->id)->toBe($user->id);
});

test('page has updater relationship', function () {
    $user = User::factory()->create();
    $page = Page::factory()->create(['updated_by' => $user->id]);

    expect($page->updater)->toBeInstanceOf(User::class)
        ->and($page->updater->id)->toBe($user->id);
});

test('page has images relationship', function () {
    $page = Page::factory()->create();
    $image = PageImage::factory()->create(['page_id' => $page->id]);

    expect($page->images)->toHaveCount(1)
        ->and($page->images->first()->id)->toBe($image->id);
});

test('page has menu items relationship', function () {
    $page = Page::factory()->create();
    $menuItem = MenuItem::factory()->create(['page_id' => $page->id]);

    expect($page->menuItems)->toHaveCount(1)
        ->and($page->menuItems->first()->id)->toBe($menuItem->id);
});

test('published scope filters published pages', function () {
    Page::factory()->create(['is_published' => true, 'published_at' => now()->subDay()]);
    Page::factory()->create(['is_published' => false]);
    Page::factory()->create(['is_published' => true, 'published_at' => now()->addDay()]);

    $publishedPages = Page::published()->get();

    expect($publishedPages)->toHaveCount(1);
});

test('draft scope filters draft pages', function () {
    Page::factory()->create(['is_published' => true, 'published_at' => now()->subDay()]);
    Page::factory()->create(['is_published' => false]);

    $draftPages = Page::draft()->get();

    expect($draftPages)->toHaveCount(1);
});

test('page can be published', function () {
    $page = Page::factory()->create(['is_published' => false]);

    $page->publish();

    expect($page->is_published)->toBeTrue()
        ->and($page->published_at)->not->toBeNull();
});

test('page can be unpublished', function () {
    $page = Page::factory()->create(['is_published' => true]);

    $page->unpublish();

    expect($page->is_published)->toBeFalse();
});

test('excerpt accessor returns truncated content', function () {
    $page = Page::factory()->create([
        'content' => str_repeat('Lorem ipsum dolor sit amet. ', 50),
    ]);

    expect($page->excerpt)->toBeString()
        ->and(strlen($page->excerpt))->toBeLessThanOrEqual(203); // 200 chars + "..."
});

test('page uses soft deletes', function () {
    $page = Page::factory()->create();
    $pageId = $page->id;

    $page->delete();

    expect(Page::find($pageId))->toBeNull()
        ->and(Page::withTrashed()->find($pageId))->not->toBeNull();
});

