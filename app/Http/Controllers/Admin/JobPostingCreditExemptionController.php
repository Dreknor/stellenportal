<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\JobPostingCreditExemption;
use Illuminate\Http\Request;

class JobPostingCreditExemptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exemptions = JobPostingCreditExemption::with('creator')
            ->orderBy('employment_type')
            ->orderBy('applies_to')
            ->paginate(20);

        return view('admin.job-posting-credit-exemptions.index', compact('exemptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employmentTypes = [
            JobPosting::EMPLOYMENT_TYPE_FULL_TIME => 'Vollzeit',
            JobPosting::EMPLOYMENT_TYPE_PART_TIME => 'Teilzeit',
            JobPosting::EMPLOYMENT_TYPE_MINI_JOB => 'Minijob',
            JobPosting::EMPLOYMENT_TYPE_INTERNSHIP => 'Praktikum',
            JobPosting::EMPLOYMENT_TYPE_APPRENTICESHIP => 'Ausbildung',
            JobPosting::EMPLOYMENT_TYPE_VOLUNTEER => 'Ehrenamt',
        ];

        $appliesTo = [
            JobPostingCreditExemption::APPLIES_TO_ALL => 'Alle Organisationen',
            JobPostingCreditExemption::APPLIES_TO_COOPERATIVE_MEMBERS_ONLY => 'Nur Genossenschaftsmitglieder',
        ];

        return view('admin.job-posting-credit-exemptions.create', compact('employmentTypes', 'appliesTo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employment_type' => 'required|in:full_time,part_time,mini_job,internship,apprenticeship,volunteer',
            'applies_to' => 'required|in:all,cooperative_members_only',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        try {
            JobPostingCreditExemption::create($validated);

            return redirect()->route('admin.job-posting-credit-exemptions.index')
                ->with('success', 'Guthabenausnahme wurde erfolgreich erstellt.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Fehler beim Erstellen der Guthabenausnahme. Diese Kombination existiert möglicherweise bereits.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(JobPostingCreditExemption $jobPostingCreditExemption)
    {
        $jobPostingCreditExemption->load('creator');
        return view('admin.job-posting-credit-exemptions.show', compact('jobPostingCreditExemption'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobPostingCreditExemption $jobPostingCreditExemption)
    {
        $employmentTypes = [
            JobPosting::EMPLOYMENT_TYPE_FULL_TIME => 'Vollzeit',
            JobPosting::EMPLOYMENT_TYPE_PART_TIME => 'Teilzeit',
            JobPosting::EMPLOYMENT_TYPE_MINI_JOB => 'Minijob',
            JobPosting::EMPLOYMENT_TYPE_INTERNSHIP => 'Praktikum',
            JobPosting::EMPLOYMENT_TYPE_APPRENTICESHIP => 'Ausbildung',
            JobPosting::EMPLOYMENT_TYPE_VOLUNTEER => 'Ehrenamt',
        ];

        $appliesTo = [
            JobPostingCreditExemption::APPLIES_TO_ALL => 'Alle Organisationen',
            JobPostingCreditExemption::APPLIES_TO_COOPERATIVE_MEMBERS_ONLY => 'Nur Genossenschaftsmitglieder',
        ];

        return view('admin.job-posting-credit-exemptions.edit', compact('jobPostingCreditExemption', 'employmentTypes', 'appliesTo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobPostingCreditExemption $jobPostingCreditExemption)
    {
        $validated = $request->validate([
            'employment_type' => 'required|in:full_time,part_time,mini_job,internship,apprenticeship,volunteer',
            'applies_to' => 'required|in:all,cooperative_members_only',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        try {
            $jobPostingCreditExemption->update($validated);

            return redirect()->route('admin.job-posting-credit-exemptions.index')
                ->with('success', 'Guthabenausnahme wurde erfolgreich aktualisiert.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Fehler beim Aktualisieren der Guthabenausnahme. Diese Kombination existiert möglicherweise bereits.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobPostingCreditExemption $jobPostingCreditExemption)
    {
        $jobPostingCreditExemption->delete();

        return redirect()->route('admin.job-posting-credit-exemptions.index')
            ->with('success', 'Guthabenausnahme wurde erfolgreich gelöscht.');
    }

    /**
     * Toggle the active status of an exemption
     */
    public function toggle(JobPostingCreditExemption $jobPostingCreditExemption)
    {
        $jobPostingCreditExemption->update([
            'is_active' => !$jobPostingCreditExemption->is_active
        ]);

        $status = $jobPostingCreditExemption->is_active ? 'aktiviert' : 'deaktiviert';

        return back()->with('success', "Guthabenausnahme wurde erfolgreich {$status}.");
    }
}
