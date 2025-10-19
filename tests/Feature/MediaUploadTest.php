<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_can_have_header_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $user->organizations()->attach($organization);

        $file = UploadedFile::fake()->image('header.jpg');

        // Use the update route which handles media uploads
        $response = $this->actingAs($user)->put(
            route('organizations.update', $organization),
            [
                'name' => $organization->name,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'website' => $organization->website,
                'description' => $organization->description,
                'street' => $organization->address->street ?? 'Test Street',
                'number' => $organization->address->number ?? '1',
                'city' => $organization->address->city ?? 'Test City',
                'zip_code' => $organization->address->zip_code ?? '12345',
                'header_image' => $file,
            ]
        );

        $response->assertRedirect();
        $organization->refresh();
        $this->assertNotNull($organization->getFirstMedia('header_image'));
    }

    public function test_organization_header_image_is_replaced_when_uploading_new_one(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $user->organizations()->attach($organization);

        // Add first image
        $firstFile = UploadedFile::fake()->image('header1.jpg');
        $organization->addMedia($firstFile)->toMediaCollection('header_image');

        $this->assertCount(1, $organization->getMedia('header_image'));

        // Add second image - spatie-media-library replaces single collection items
        $secondFile = UploadedFile::fake()->image('header2.jpg');
        $organization->addMedia($secondFile)->toMediaCollection('header_image');

        $organization->refresh();

        // Spatie media library with single collection should keep only 1 item
        $this->assertCount(1, $organization->getMedia('header_image'));
    }
}
