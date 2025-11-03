<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FooterSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $footerSettings = FooterSetting::latest()->paginate(10);
        return view('admin.footer-settings.index', compact('footerSettings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.footer-settings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'links' => 'nullable|array',
            'links.*.title' => 'required|string|max:255',
            'links.*.url' => 'required|url|max:255',
            'background_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'text_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'link_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'is_active' => 'boolean',
        ]);

        // If this should be active, deactivate all others
        if ($request->boolean('is_active')) {
            FooterSetting::where('is_active', true)->update(['is_active' => false]);
        }

        $data = [
            'content' => $validated['content'] ?? null,
            'links' => $validated['links'] ?? null,
            'background_color' => $validated['background_color'] ?? '#ffffff',
            'text_color' => $validated['text_color'] ?? '#6b7280',
            'link_color' => $validated['link_color'] ?? '#2563eb',
            'is_active' => $request->boolean('is_active'),
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('footer-logos', 'public');
        }

        FooterSetting::create($data);

        return redirect()
            ->route('admin.footer-settings.index')
            ->with('success', 'Footer-Einstellung erfolgreich erstellt.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FooterSetting $footerSetting)
    {
        return view('admin.footer-settings.show', compact('footerSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FooterSetting $footerSetting)
    {
        return view('admin.footer-settings.edit', compact('footerSetting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FooterSetting $footerSetting)
    {
        $validated = $request->validate([
            'content' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'links' => 'nullable|array',
            'links.*.title' => 'required|string|max:255',
            'links.*.url' => 'required|url|max:255',
            'background_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'text_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'link_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'is_active' => 'boolean',
        ]);

        // If this should be active, deactivate all others
        if ($request->boolean('is_active')) {
            FooterSetting::where('id', '!=', $footerSetting->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $data = [
            'content' => $validated['content'] ?? null,
            'links' => $validated['links'] ?? null,
            'background_color' => $validated['background_color'] ?? '#ffffff',
            'text_color' => $validated['text_color'] ?? '#6b7280',
            'link_color' => $validated['link_color'] ?? '#2563eb',
            'is_active' => $request->boolean('is_active'),
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            $footerSetting->deleteLogo();
            $data['logo_path'] = $request->file('logo')->store('footer-logos', 'public');
        }

        $footerSetting->update($data);

        return redirect()
            ->route('admin.footer-settings.index')
            ->with('success', 'Footer-Einstellung erfolgreich aktualisiert.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FooterSetting $footerSetting)
    {
        $footerSetting->deleteLogo();
        $footerSetting->delete();

        return redirect()
            ->route('admin.footer-settings.index')
            ->with('success', 'Footer-Einstellung erfolgreich gelöscht.');
    }

    /**
     * Activate a footer setting
     */
    public function activate(FooterSetting $footerSetting)
    {
        // Deactivate all others
        FooterSetting::where('id', '!=', $footerSetting->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $footerSetting->update(['is_active' => true]);

        return redirect()
            ->route('admin.footer-settings.index')
            ->with('success', 'Footer-Einstellung aktiviert.');
    }
}

