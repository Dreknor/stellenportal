<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $jobPostings = JobPosting::with('facility.address')
            ->where('status', 'active')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->get();

        // CMS Pages
        $pages = Page::published()->orderBy('updated_at', 'desc')->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        // Homepage
        $xml .= '<url>';
        $xml .= '<loc>' . url('/') . '</loc>';
        $xml .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';

        // Jobs Index
        $xml .= '<url>';
        $xml .= '<loc>' . route('public.jobs.index') . '</loc>';
        $xml .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>0.9</priority>';
        $xml .= '</url>';

        // Individual Job Postings
        foreach ($jobPostings as $job) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars(route('public.jobs.show', $job)) . '</loc>';
            $xml .= '<lastmod>' . $job->updated_at->toAtomString() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';

            // Add image if available
            $headerImage = $job->facility->getFirstMediaUrl('header_image')
                ?: $job->facility->getFirstMediaUrl('header')
                ?: $job->facility->getFirstMediaUrl('logo');

            if ($headerImage) {
                $xml .= '<image:image>';
                $xml .= '<image:loc>' . htmlspecialchars($headerImage) . '</image:loc>';
                $xml .= '<image:title>' . htmlspecialchars($job->title) . '</image:title>';
                $xml .= '<image:caption>' . htmlspecialchars($job->facility->name) . '</image:caption>';
                $xml .= '</image:image>';
            }

            $xml .= '</url>';
        }

        // CMS Pages
        foreach ($pages as $page) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars(route('pages.show', $page->slug)) . '</loc>';
            $xml .= '<lastmod>' . $page->updated_at->toAtomString() . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}

