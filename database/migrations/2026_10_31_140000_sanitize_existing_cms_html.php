<?php

use App\Models\ContentBlock;
use App\Support\HtmlSanitizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Strip scripts and event handlers from CMS content stored before sanitizing on save.
     */
    public function up(): void
    {
        DB::table('pages')->whereNotNull('content')->orderBy('id')->each(function ($page) {
            DB::table('pages')->where('id', $page->id)->update(['content' => HtmlSanitizer::clean($page->content)]);
        });

        DB::table('content_blocks')->whereIn('type', ContentBlock::HTML_TYPES)->whereNotNull('content')->orderBy('id')->each(function ($block) {
            DB::table('content_blocks')->where('id', $block->id)->update(['content' => HtmlSanitizer::clean($block->content)]);
        });
    }

    public function down(): void
    {
        // Removed markup cannot be restored
    }
};
