<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // No real HTTP calls in tests (Kavenegar, Zarinpal…): every request must be faked with Http::fake().
        Http::preventStrayRequests();
    }
}
