<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\JobPostingInteraction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobPostingInteractionController extends Controller
{
    /**
     * Track a job posting interaction
     */
    public function track(Request $request, JobPosting $jobPosting): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:view,apply_click,email_reveal,phone_reveal,download',
        ]);

        try {
            JobPostingInteraction::track(
                $jobPosting->id,
                $request->type,
                session()->getId()
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}


