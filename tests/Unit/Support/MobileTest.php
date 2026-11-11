<?php

namespace Tests\Unit\Support;

use App\Support\Mobile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MobileTest extends TestCase
{
    public static function validNumbers(): array
    {
        return [
            'standard' => ['09121234567'],
            'persian digits' => ['۰۹۱۲۱۲۳۴۵۶۷'],
            'arabic digits' => ['٠٩١٢١٢٣٤٥٦٧'],
            'plus 98' => ['+989121234567'],
            'plus 98 with spaces' => ['+98 912 123 4567'],
            '0098' => ['00989121234567'],
            '98 without plus' => ['989121234567'],
            'without leading zero' => ['9121234567'],
            'dashes' => ['0912-123-4567'],
            'persian with plus' => ['+۹۸۹۱۲۱۲۳۴۵۶۷'],
        ];
    }

    #[DataProvider('validNumbers')]
    public function test_valid_numbers_are_normalized(string $input): void
    {
        $this->assertSame('09121234567', Mobile::normalize($input));
        $this->assertTrue(Mobile::isValid($input));
    }

    public static function invalidNumbers(): array
    {
        return [
            'empty' => [''],
            'too short' => ['0912123456'],
            'too long' => ['091212345678'],
            'landline' => ['02112345678'],
            'letters' => ['abc'],
            'not starting with 9' => ['08121234567'],
            'other country' => ['+14155552671'],
        ];
    }

    #[DataProvider('invalidNumbers')]
    public function test_invalid_numbers_return_null(string $input): void
    {
        $this->assertNull(Mobile::normalize($input));
        $this->assertFalse(Mobile::isValid($input));
    }

    public function test_null_input(): void
    {
        $this->assertNull(Mobile::normalize(null));
    }
}
