<?php

namespace App\Support;

/**
 * Iran's 31 provinces (for the address form). Sorted alphabetically in Persian.
 */
final class IranProvinces
{
    /** @return array<int, string> */
    public static function all(): array
    {
        return [
            'آذربایجان شرقی',
            'آذربایجان غربی',
            'اردبیل',
            'اصفهان',
            'البرز',
            'ایلام',
            'بوشهر',
            'تهران',
            'چهارمحال و بختیاری',
            'خراسان جنوبی',
            'خراسان رضوی',
            'خراسان شمالی',
            'خوزستان',
            'زنجان',
            'سمنان',
            'سیستان و بلوچستان',
            'فارس',
            'قزوین',
            'قم',
            'کردستان',
            'کرمان',
            'کرمانشاه',
            'کهگیلویه و بویراحمد',
            'گلستان',
            'گیلان',
            'لرستان',
            'مازندران',
            'مرکزی',
            'هرمزگان',
            'همدان',
            'یزد',
        ];
    }
}
