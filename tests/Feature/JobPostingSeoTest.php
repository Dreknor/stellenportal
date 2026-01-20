<?php

use App\Models\JobPosting;
use App\Models\Facility;
use App\Models\Address;

test('seo keywords array includes custom keywords', function () {
    $facility = Facility::factory()->create();
    $jobPosting = JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'seo_keywords' => 'Grundschullehrer, Mathematik, Sachsen',
    ]);

    $keywords = $jobPosting->getSeoKeywordsArray();

    expect($keywords)->toContain('Grundschullehrer');
    expect($keywords)->toContain('Mathematik');
    expect($keywords)->toContain('Sachsen');
});

test('seo keywords array includes location', function () {
    $facility = Facility::factory()->create();
    $address = Address::factory()->create([
        'addressable_type' => Facility::class,
        'addressable_id' => $facility->id,
        'city' => 'Dresden',
    ]);
    $facility->address()->save($address);

    $jobPosting = JobPosting::factory()->create([
        'facility_id' => $facility->id,
    ]);

    $keywords = $jobPosting->getSeoKeywordsArray();

    expect($keywords)->toContain('Dresden');
});

test('seo keywords array includes common school terms', function () {
    $facility = Facility::factory()->create();
    $jobPosting = JobPosting::factory()->create([
        'facility_id' => $facility->id,
    ]);

    $keywords = $jobPosting->getSeoKeywordsArray();

    $expectedTerms = [
        'Stelle', 'Job', 'Stellenangebot', 'Stellenanzeige',
        'Bewerbung', 'Karriere', 'Schule', 'Bildung',
        'Pädagogik', 'Lehrkraft', 'Lehrer', 'Schuljob', 'Schulstelle',
    ];

    foreach ($expectedTerms as $term) {
        expect($keywords)->toContain($term);
    }
});

test('seo keywords array includes employment type', function () {
    $facility = Facility::factory()->create();
    $jobPosting = JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'employment_type' => JobPosting::EMPLOYMENT_TYPE_FULL_TIME,
    ]);

    $keywords = $jobPosting->getSeoKeywordsArray();

    expect($keywords)->toContain('Vollzeit');
});

test('seo keywords array includes job category', function () {
    $facility = Facility::factory()->create();
    $jobPosting = JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'job_category' => 'Pädagogik',
    ]);

    $keywords = $jobPosting->getSeoKeywordsArray();

    expect($keywords)->toContain('Pädagogik');
});

test('seo keywords array removes duplicates', function () {
    $facility = Facility::factory()->create();
    $address = Address::factory()->create([
        'addressable_type' => Facility::class,
        'addressable_id' => $facility->id,
        'city' => 'Dresden',
    ]);
    $facility->address()->save($address);

    $jobPosting = JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'seo_keywords' => 'Dresden, Lehrer, Schule',
    ]);

    $keywords = $jobPosting->getSeoKeywordsArray();

    // Count occurrences - each should only appear once
    $dresdenCount = count(array_filter($keywords, fn($k) => $k === 'Dresden'));
    expect($dresdenCount)->toBe(1);

    $lehrerCount = count(array_filter($keywords, fn($k) => $k === 'Lehrer'));
    expect($lehrerCount)->toBe(1);

    $schuleCount = count(array_filter($keywords, fn($k) => $k === 'Schule'));
    expect($schuleCount)->toBe(1);
});

test('seo meta description has correct format and length', function () {
    $facility = Facility::factory()->create([
        'name' => 'Beispielschule Dresden',
    ]);
    $address = Address::factory()->create([
        'addressable_type' => Facility::class,
        'addressable_id' => $facility->id,
        'city' => 'Dresden',
    ]);
    $facility->address()->save($address);

    $jobPosting = JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'title' => 'Grundschullehrer',
        'employment_type' => JobPosting::EMPLOYMENT_TYPE_FULL_TIME,
        'description' => 'Wir suchen eine engagierte Lehrkraft für unsere Grundschule in Dresden.',
    ]);

    $metaDescription = $jobPosting->getSeoMetaDescription();

    expect($metaDescription)->toContain('Grundschullehrer');
    expect($metaDescription)->toContain('Vollzeit');
    expect($metaDescription)->toContain('Dresden');
    expect($metaDescription)->toContain('Beispielschule Dresden');
    expect(mb_strlen($metaDescription))->toBeLessThanOrEqual(155);
});

test('seo meta description works without location', function () {
    $facility = Facility::factory()->create([
        'name' => 'Beispielschule',
    ]);

    $jobPosting = JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'title' => 'Grundschullehrer',
        'employment_type' => JobPosting::EMPLOYMENT_TYPE_PART_TIME,
        'description' => 'Wir suchen eine Teilzeit-Lehrkraft.',
    ]);

    $metaDescription = $jobPosting->getSeoMetaDescription();

    expect($metaDescription)->toContain('Grundschullehrer');
    expect($metaDescription)->toContain('Teilzeit');
    expect($metaDescription)->toContain('Beispielschule');
    expect(mb_strlen($metaDescription))->toBeLessThanOrEqual(155);
});

test('seo keywords field is fillable', function () {
    $facility = Facility::factory()->create();

    $jobPosting = JobPosting::create([
        'facility_id' => $facility->id,
        'created_by' => 1,
        'title' => 'Test Job',
        'description' => 'Test description',
        'employment_type' => JobPosting::EMPLOYMENT_TYPE_FULL_TIME,
        'seo_keywords' => 'test, keywords',
        'status' => JobPosting::STATUS_DRAFT,
    ]);

    expect($jobPosting->seo_keywords)->toBe('test, keywords');
});
