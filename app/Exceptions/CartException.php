<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A cart action that cannot be done (product unavailable, not enough stock…).
 * The message is Persian and safe to show to the customer.
 */
class CartException extends RuntimeException
{
}
