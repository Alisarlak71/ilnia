<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * An order cannot be placed (empty cart, cart changed, invalid address…).
 * The message is Persian and safe to show to the customer.
 */
class OrderException extends RuntimeException
{
}
