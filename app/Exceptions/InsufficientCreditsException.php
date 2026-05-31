<?php

namespace App\Exceptions;

/**
 * Wird geworfen, wenn ein Credit-verbrauchender Vorgang nicht genügend
 * Guthaben auf dem Balance-Konto vorfindet.
 */
class InsufficientCreditsException extends \RuntimeException
{
    public function __construct(string $message = 'Nicht genügend Guthaben vorhanden.', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}

