<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Accepts relative paths/anchors and absolute http(s), mailto and tel URLs.
 * Rejects schemes like javascript: or data: that execute when clicked.
 */
class SafeUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $url = trim((string) $value);

        // Relative paths and anchors, but not protocol-relative "//host"
        if (preg_match('#^(/(?!/)|\#|\?)#', $url)) {
            return;
        }

        if (preg_match('#^(https?://[^\s/$.?\#][^\s]*|mailto:[^\s]+|tel:[+\d\s()/-]+)$#i', $url)) {
            return;
        }

        $fail('Die URL muss mit http://, https://, mailto:, tel: oder / beginnen.');
    }
}
