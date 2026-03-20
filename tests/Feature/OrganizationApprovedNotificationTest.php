<?php

use App\Mail\OrganizationApprovedMail;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    Mail::fake();

    $adminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    $permission = Permission::firstOrCreate(['name' => 'admin edit organizations', 'guard_name' => 'web']);
    $adminRole->givePermissionTo($permission);

    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

test('Nutzer wird per E-Mail benachrichtigt, wenn seine Organisation freigeschaltet wird', function () {
    $admin = User::factory()->create([
        'email_verified_at' => now(),
        'change_password' => false,
    ]);
    $admin->assignRole('Super Admin');
    $admin->givePermissionTo('admin edit organizations');

    $organization = Organization::factory()->create(['is_approved' => false]);

    $user = User::factory()->create();
    $user->organizations()->attach($organization->id);

    $this->actingAs($admin)->post(route('admin.organizations.approve', $organization));

    Mail::assertQueued(OrganizationApprovedMail::class, function ($mail) use ($user, $organization) {
        return $mail->hasTo($user->email)
            && $mail->user->id === $user->id
            && $mail->organization->id === $organization->id;
    });
});

test('Alle Nutzer einer Organisation werden benachrichtigt, wenn die Organisation freigeschaltet wird', function () {
    $admin = User::factory()->create([
        'email_verified_at' => now(),
        'change_password' => false,
    ]);
    $admin->assignRole('Super Admin');
    $admin->givePermissionTo('admin edit organizations');

    $organization = Organization::factory()->create(['is_approved' => false]);

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();

    $user1->organizations()->attach($organization->id);
    $user2->organizations()->attach($organization->id);
    $user3->organizations()->attach($organization->id);

    $this->actingAs($admin)->post(route('admin.organizations.approve', $organization));

    Mail::assertQueued(OrganizationApprovedMail::class, 3);

    Mail::assertQueued(OrganizationApprovedMail::class, fn ($mail) => $mail->hasTo($user1->email));
    Mail::assertQueued(OrganizationApprovedMail::class, fn ($mail) => $mail->hasTo($user2->email));
    Mail::assertQueued(OrganizationApprovedMail::class, fn ($mail) => $mail->hasTo($user3->email));
});

test('Keine E-Mail wird gesendet, wenn eine bereits freigeschaltete Organisation erneut bestätigt werden soll', function () {
    $admin = User::factory()->create([
        'email_verified_at' => now(),
        'change_password' => false,
    ]);
    $admin->assignRole('Super Admin');
    $admin->givePermissionTo('admin edit organizations');

    $organization = Organization::factory()->approved()->create();

    $user = User::factory()->create();
    $user->organizations()->attach($organization->id);

    $this->actingAs($admin)->post(route('admin.organizations.approve', $organization));

    Mail::assertNothingQueued();
});

test('Keine E-Mail wird gesendet, wenn eine Organisation ohne Nutzer freigeschaltet wird', function () {
    $admin = User::factory()->create([
        'email_verified_at' => now(),
        'change_password' => false,
    ]);
    $admin->assignRole('Super Admin');
    $admin->givePermissionTo('admin edit organizations');

    $organization = Organization::factory()->create(['is_approved' => false]);

    $this->actingAs($admin)->post(route('admin.organizations.approve', $organization));

    Mail::assertNothingQueued();
});

test('OrganizationApprovedMail enthält die korrekten Nutzer- und Organisationsdaten', function () {
    $user = User::factory()->create([
        'first_name' => 'Max',
        'last_name' => 'Mustermann',
    ]);
    /** @var Organization $organization */
    $organization = Organization::factory()->create(['name' => 'Testschule Dresden']);

    $mailable = new OrganizationApprovedMail($user, $organization);
    $mailable->to($user->email);

    $mailable->assertHasTo($user->email);
    $mailable->assertSeeInHtml('Max');
    $mailable->assertSeeInHtml('Mustermann');
    $mailable->assertSeeInHtml('Testschule Dresden');
    $mailable->assertSeeInHtml('freigeschaltet');
});

