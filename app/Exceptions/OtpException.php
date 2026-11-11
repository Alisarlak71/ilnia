<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A login-code action that cannot be done. The message is Persian and safe to show.
 */
class OtpException extends RuntimeException
{
}
