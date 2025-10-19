<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create(
            ['email' => 'test@example.com', 'email_verified_at' => now(),
            'change_password' => false
        ]);

        $response = $this->actingAs($user)->get('/settings/profile');

        $response->assertStatus(200);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create(
            ['email' => 'test@example.com', 'email_verified_at' => now(),
                'change_password' => false
            ]

        );

        $response = $this->actingAs($user)->put('/settings/profile', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $user->email,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('settings.profile.edit'));

        $user->refresh();

        $this->assertSame('Test', $user->first_name);
        $this->assertSame('User', $user->last_name);
    }

    public function test_email_verification_status_is_unchanged_when_email_address_is_unchanged(): void
    {
        $user = User::factory()->create(
            ['email' => 'test@example.com', 'email_verified_at' => now(),
                'change_password' => false
            ]
        );

        $response = $this->actingAs($user)->put('/settings/profile', [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
        ]);

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

}
