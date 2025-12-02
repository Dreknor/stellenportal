<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageImageController extends Controller
{
    /**
     * Display a listing of images for a page.
     */
    public function index(Page $page)
    {
        $images = $page->images()->ordered()->get();

        return view('admin.pages.images.index', compact('page', 'images'));
    }

    /**
     * Store a newly uploaded image.
     */
    public function store(Request $request, Page $page)
    {
        $validated = $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'], // 5MB
            'alt_text' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('image');
        $originalFilename = $file->getClientOriginalName();
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('pages/' . $page->id, $filename, 'public');

        $image = $page->images()->create([
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'alt_text' => $validated['alt_text'] ?? null,
            'title' => $validated['title'] ?? null,
            'order' => $page->images()->max('order') + 1,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'image' => $image,
                'message' => 'Bild erfolgreich hochgeladen.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Bild erfolgreich hochgeladen.');
    }

    /**
     * Update the specified image.
     */
    public function update(Request $request, Page $page, PageImage $image)
    {
        $validated = $request->validate([
            'alt_text' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $image->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'image' => $image,
                'message' => 'Bild erfolgreich aktualisiert.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Bild erfolgreich aktualisiert.');
    }

    /**
     * Remove the specified image.
     */
    public function destroy(Request $request, Page $page, PageImage $image)
    {
        $image->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bild erfolgreich gelöscht.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Bild erfolgreich gelöscht.');
    }

    /**
     * Reorder images.
     */
    public function reorder(Request $request, Page $page)
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'exists:page_images,id'],
        ]);

        foreach ($validated['order'] as $index => $imageId) {
            PageImage::where('id', $imageId)
                ->where('page_id', $page->id)
                ->update(['order' => $index]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reihenfolge erfolgreich aktualisiert.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Reihenfolge erfolgreich aktualisiert.');
    }
}
