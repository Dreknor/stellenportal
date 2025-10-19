<?php

namespace Tests\Feature;

use App\Mail\CreditPurchasedConfirmationMail;
use App\Mail\UserCreatedMail;
use App\Models\CreditPackage;
use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_purchased_confirmation_mail_is_sent(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $facility = Facility::factory()->create();
        $user->facilities()->attach($facility);

        $package = CreditPackage::factory()->create(['is_active' => true]);

        $this->actingAs($user)->post(
            route('credits.facility.purchase.store', $facility),
            ['credit_package_id' => $package->id]
        );

        Mail::assertQueued(CreditPurchasedConfirmationMail::class);
    }

    public function test_user_created_mail_is_sent(): void
    {
        Mail::fake();

        $admin = User::factory()->create();
        $organization = Organization::factory()->create();
        $admin->organizations()->attach($organization);

        // Create a new user via the organization user controller
        $this->actingAs($admin)->post(
            route('organizations.users.store', $organization),
            [
                'first_name' => 'Max',
                'last_name' => 'Mustermann',
                'email' => 'test@example.com',
            ]
        );

        Mail::assertQueued(UserCreatedMail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    }
}
