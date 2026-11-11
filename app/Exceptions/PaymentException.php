<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A payment step could not be done. The message is Persian and safe to show to the customer.
 */
class PaymentException extends RuntimeException
{
}
