<?php

namespace App\Support\Http;

use RuntimeException;

/** A request SafeHttp refused to make, or that failed. `reason` is a stable code for messages. */
class UnsafeRequestException extends RuntimeException
{
    public const INVALID_URL = 'invalid_url';

    public const PRIVATE_ADDRESS = 'private_address';

    public const UNREACHABLE = 'unreachable';

    public const TOO_LARGE = 'too_large';

    public const TOO_MANY_REDIRECTS = 'too_many_redirects';

    public const REDIRECT_BLOCKED = 'redirect_blocked';

    public function __construct(public readonly string $reason, string $message = '')
    {
        parent::__construct($message ?: $reason);
    }
}
