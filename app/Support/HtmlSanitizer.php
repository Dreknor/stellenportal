<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer as SymfonyHtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Removes scripts, event handlers and dangerous URLs from CMS HTML
 * while keeping the formatting produced by TinyMCE.
 */
class HtmlSanitizer
{
    private static ?SymfonyHtmlSanitizer $sanitizer = null;

    public static function clean(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return $html;
        }

        return self::sanitizer()->sanitize($html);
    }

    private static function sanitizer(): SymfonyHtmlSanitizer
    {
        if (self::$sanitizer === null) {
            $config = (new HtmlSanitizerConfig())
                ->allowSafeElements()
                ->allowAttribute('class', '*')
                ->allowAttribute('style', '*')
                ->allowAttribute('id', '*')
                ->allowAttribute('target', 'a')
                ->allowAttribute('rel', 'a')
                ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
                ->allowRelativeLinks()
                ->allowMediaSchemes(['http', 'https'])
                ->allowRelativeMedias()
                ->withMaxInputLength(500000);

            self::$sanitizer = new SymfonyHtmlSanitizer($config);
        }

        return self::$sanitizer;
    }
}
