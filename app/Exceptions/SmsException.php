<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * An SMS could not be sent. The message is technical (for logs);
 * show the customer a general Persian message instead.
 */
class SmsException extends RuntimeException
{
}
