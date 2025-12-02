<?php

use App\Models\Page;

test('published page can be viewed', function () {
    $page = Page::factory()->published()->create([
        'slug' => 'test-page',
        'title' => 'Test Page',
        'content' => 'Test content',
    ]);

    $response = $this->get(route('pages.show', $page->slug));

    $response->assertStatus(200);
    $response->assertViewIs('public.pages.show');
    $response->assertSee('Test Page');
    $response->assertSee('Test content');
});

test('unpublished page returns 404', function () {
    $page = Page::factory()->draft()->create(['slug' => 'draft-page']);

    $response = $this->get(route('pages.show', $page->slug));

    $response->assertStatus(404);
});

test('non-existent page returns 404', function () {
    $response = $this->get(route('pages.show', 'non-existent-page'));

    $response->assertStatus(404);
});

test('page displays meta title', function () {
    $page = Page::factory()->published()->create([
        'slug' => 'seo-page',
        'meta_title' => 'SEO Title',
    ]);

    $response = $this->get(route('pages.show', $page->slug));

    $response->assertSee('SEO Title', false);
});

test('page displays meta description', function () {
    $page = Page::factory()->published()->create([
        'slug' => 'seo-page',
        'meta_description' => 'SEO Description',
    ]);

    $response = $this->get(route('pages.show', $page->slug));

    $response->assertSee('SEO Description', false);
});

test('page with images displays images', function () {
    $page = Page::factory()->published()->create();
    $image = \App\Models\PageImage::factory()->create([
        'page_id' => $page->id,
        'alt_text' => 'Test Image',
    ]);

    $response = $this->get(route('pages.show', $page->slug));

    $response->assertSee('Test Image');
});

test('only published pages are accessible', function () {
    $publishedPage = Page::factory()->published()->create(['slug' => 'published']);
    $draftPage = Page::factory()->draft()->create(['slug' => 'draft']);

    $this->get(route('pages.show', $publishedPage->slug))->assertStatus(200);
    $this->get(route('pages.show', $draftPage->slug))->assertStatus(404);
});

test('page published in future returns 404', function () {
    $page = Page::factory()->create([
        'slug' => 'future-page',
        'is_published' => true,
        'published_at' => now()->addDay(),
    ]);

    $response = $this->get(route('pages.show', $page->slug));

    $response->assertStatus(404);
});

