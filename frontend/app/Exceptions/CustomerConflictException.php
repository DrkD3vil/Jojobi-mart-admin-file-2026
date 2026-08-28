<?php

namespace App\Exceptions;

/**
 * Thrown when a phone number and an email address independently match two
 * different existing `customers` rows -- CustomerMatcher can't tell which
 * one the person actually means, so it refuses to guess.
 */
class CustomerConflictException extends \RuntimeException
{
    public function __construct(string $message = 'We found two different accounts matching this phone and email.')
    {
        parent::__construct($message);
    }
}
