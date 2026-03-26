<?php

namespace App\Observers;

use App\Mail\OrganizationApprovedMail;
use App\Models\Organization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrganizationObserver
{
    /**
     * Handle the Organization "updated" event.
     * Sends an approval notification to all organization users when is_approved changes to true.
     */
    public function updated(Organization $organization): void
    {
        if ($organization->wasChanged('is_approved') && $organization->is_approved) {
            $organization->load('users');

            foreach ($organization->users as $user) {
                Log::debug("Queueing approval email for user {$user->email} regarding organization {$organization->name}");
                Mail::to($user->email)->queue(new OrganizationApprovedMail($user, $organization));

            }
        }
    }
}

